<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Programs extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic models
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_meal_plans_model');
        $this->load->model('dietetic/dietetic_foods_model');
        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * List all programs
     */
    public function index()
    {
        $data['title'] = _l('dietetic_programs');
        $data['programs'] = $this->dietetic_programs_model->get_all();

        // Calculate statistics
        $all_programs = $data['programs'];
        $data['total_count'] = count($all_programs);
        $data['active_count'] = 0;
        $data['completed_count'] = 0;
        $data['this_month_count'] = 0;

        $current_month = date('Y-m');

        foreach ($all_programs as $program) {
            if ($program->status == 'active') {
                $data['active_count']++;
            } elseif ($program->status == 'completed') {
                $data['completed_count']++;
            }

            if (substr($program->start_date, 0, 7) == $current_month) {
                $data['this_month_count']++;
            }
        }

        $this->load->view('admin/programs/list', $data);
    }

    /**
     * View program detail
     *
     * @param int $id
     */
    public function view($id)
    {
        $data['program'] = $this->dietetic_programs_model->get($id);

        if (!$data['program']) {
            show_404();
        }

        // Log activity - Program viewed
        log_activity('Programme consulté : "' . $data['program']->program_name . '" (ID: ' . $id . ')');

        $data['title'] = $data['program']->program_name;
        $data['patient'] = $this->dietetic_patients_model->get($data['program']->patient_id);
        $data['meal_plans'] = $this->dietetic_programs_model->get_meal_plans($id);

        // Auto-calculate nutritional objectives from anamnesis data
        $data['calculated_objectives'] = null;
        if ($data['patient']) {
            $this->load->library('dietetic/Dietetic_nutrition_calculator');

            // Get patient latest measurement for current weight
            $this->db->where('patient_id', $data['patient']->id);
            $this->db->order_by('measurement_date', 'DESC');
            $this->db->limit(1);
            $latest_measurement = $this->db->get(db_prefix() . 'dietic_measurements')->row();

            $current_weight = $latest_measurement ? $latest_measurement->weight : null;

            // Calculate age from birth_date
            $age = null;
            if ($data['patient']->birth_date) {
                $birthdate = new DateTime($data['patient']->birth_date);
                $today = new DateTime();
                $age = $birthdate->diff($today)->y;
            }

            // Only calculate if we have the minimum required data
            if ($current_weight && $data['patient']->height && $age) {
                // Map activity level from patient data to calculator constants
                $activity_map = [
                    'sedentary' => Dietetic_nutrition_calculator::ACTIVITY_SEDENTARY,
                    'light' => Dietetic_nutrition_calculator::ACTIVITY_LIGHT,
                    'moderate' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
                    'active' => Dietetic_nutrition_calculator::ACTIVITY_ACTIVE,
                    'very_active' => Dietetic_nutrition_calculator::ACTIVITY_VERY_ACTIVE
                ];

                $activity_level = $activity_map[$data['patient']->activity_level] ?? Dietetic_nutrition_calculator::ACTIVITY_MODERATE;

                // Determine goal from program objective or default to maintenance
                $goal = Dietetic_nutrition_calculator::GOAL_MAINTENANCE;
                if ($data['program']->objective) {
                    $objective_lower = strtolower($data['program']->objective);
                    if (strpos($objective_lower, 'perte') !== false || strpos($objective_lower, 'perd') !== false || strpos($objective_lower, 'maigrir') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS;
                    } elseif (strpos($objective_lower, 'prise') !== false || strpos($objective_lower, 'gagn') !== false || strpos($objective_lower, 'gross') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_GAIN;
                    } elseif (strpos($objective_lower, 'muscle') !== false || strpos($objective_lower, 'muscul') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_MUSCLE_GAIN;
                    }
                }

                $patient_data = [
                    'weight' => $current_weight,
                    'height' => $data['patient']->height,
                    'age' => $age,
                    'gender' => $data['patient']->gender,
                    'activity_level' => $activity_level,
                    'goal' => $goal,
                    'waist' => $data['patient']->waist_circumference ?? null,
                    'neck' => $data['patient']->neck_circumference ?? null,
                    'hip' => $data['patient']->hip_circumference ?? null
                ];

                $data['calculated_objectives'] = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_data);
            }
        }

        // ============================================
        // Load invoices related to this program
        // ============================================
        $data['invoices'] = [];
        if (isset($data['patient']) && $data['patient'] && isset($data['patient']->client_id)) {
            // Simple query: get all invoices for this client and filter by program reference
            $this->db->select('i.*');
            $this->db->from(db_prefix() . 'invoices i');
            $this->db->where('i.clientid', $data['patient']->client_id);
            $this->db->group_start();
            $this->db->like('i.adminnote', 'Programme');
            $this->db->like('i.adminnote', 'ID: ' . $id);
            $this->db->group_end();
            $this->db->order_by('i.date', 'DESC');
            $invoices_result = $this->db->get()->result();

            // Also try to get invoices with the program tag
            $this->db->select('i.*');
            $this->db->from(db_prefix() . 'invoices i');
            $this->db->join(db_prefix() . 'taggables tg', 'tg.rel_id = i.id AND tg.rel_type = "invoice"', 'inner');
            $this->db->join(db_prefix() . 'tags t', 't.id = tg.tag_id', 'inner');
            $this->db->where('i.clientid', $data['patient']->client_id);
            $this->db->where('t.name', 'programme_' . $id);
            $invoices_tagged = $this->db->get()->result();

            // Merge results and remove duplicates
            $all_invoices = array_merge($invoices_result, $invoices_tagged);
            $unique_invoices = [];
            foreach ($all_invoices as $invoice) {
                $unique_invoices[$invoice->id] = $invoice;
            }
            $data['invoices'] = array_values($unique_invoices);
        }

        // ============================================
        // Load activity history for this program
        // ============================================
        $data['history'] = [];
        $this->db->select('*');
        $this->db->from(db_prefix() . 'activity_log');
        $this->db->group_start();
            $this->db->like('description', 'programme ' . $id);
            $this->db->or_like('description', 'program ' . $id);
            $this->db->or_like('description', 'Programme ID: ' . $id);
            $this->db->or_like('description', 'Program ID: ' . $id);
        $this->db->group_end();
        $this->db->order_by('date', 'DESC');
        $this->db->limit(50); // Last 50 activities
        $data['history'] = $this->db->get()->result();

        $this->load->view('admin/programs/view', $data);
    }

    /**
     * Create new program
     */
    public function create()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Set dietitian
            if (!isset($data['dietitian_id'])) {
                $data['dietitian_id'] = get_staff_user_id();
            }

            // Calculate billing if service is selected
            if (!empty($data['service_id']) && !empty($data['duration_months']) && !empty($data['payment_mode'])) {
                $service_id = $data['service_id'];
                $duration = (int)$data['duration_months'];
                $payment_mode = $data['payment_mode'];

                // Get service details
                $service = $this->db->get_where(db_prefix() . 'items', ['id' => $service_id])->row();

                if ($service) {
                    $monthly_price = $service->rate;
                    $subtotal = $monthly_price * $duration;

                    // Determine discount percentage
                    $discount_percent = 0;
                    if ($duration == 6) {
                        $discount_percent = $service->service_discount_6_months ?? 0;
                    } elseif ($duration == 12) {
                        $discount_percent = $service->service_discount_12_months ?? 0;
                    }

                    // Calculate total with discount
                    $discount_amount = ($subtotal * $discount_percent) / 100;
                    $total_price = $subtotal - $discount_amount;

                    // Add calculated billing data
                    $data['monthly_price'] = $monthly_price;
                    $data['total_price'] = $total_price;
                    $data['discount_applied'] = $discount_percent;
                    $data['billing_status'] = 'pending'; // Will be active after first payment

                    // Calculate end_date based on duration if not set
                    if (empty($data['end_date']) && !empty($data['start_date'])) {
                        $data['end_date'] = date('Y-m-d', strtotime($data['start_date'] . ' +' . $duration . ' months'));
                    }

                    // Set next billing date
                    if ($payment_mode == 'recurring') {
                        // First bill immediately, next one in 1 month
                        $data['next_billing_date'] = date('Y-m-d', strtotime($data['start_date'] . ' +1 month'));
                        $data['total_invoices_expected'] = $duration;
                    } else {
                        // One-time payment, no recurring
                        $data['next_billing_date'] = null;
                        $data['total_invoices_expected'] = 1;
                    }

                    log_activity('PROGRAM CREATE - Billing calculated: Service ID ' . $service_id . ', Duration ' . $duration . ' months, Total: ' . $total_price . ' FCFA');
                }
            }

            $program_id = $this->dietetic_programs_model->add($data);

            if ($program_id) {
                log_activity('PROGRAM CREATE - Program created successfully: ID ' . $program_id);

                // ============================================
                // CREATE INVOICE AUTOMATICALLY
                // ============================================
                if (!empty($data['service_id']) && !empty($data['total_price'])) {
                    try {
                        log_activity('PROGRAM CREATE - Creating invoice for program: ' . $program_id);

                        $this->load->model('invoices_model');

                        // Get patient info
                        $patient = $this->dietetic_patients_model->get($data['patient_id'], false);

                        if (!$patient || !$patient->client_id) {
                            throw new Exception('Patient or client not found');
                        }

                        // Get service info
                        $service = $this->db->get_where(db_prefix() . 'items', ['id' => $data['service_id']])->row();

                        if (!$service) {
                            throw new Exception('Service not found');
                        }

                        // Determine invoice amount based on payment mode
                        $invoice_amount = ($data['payment_mode'] == 'one_time')
                            ? $data['total_price']  // Full amount for one-time payment
                            : $data['monthly_price']; // First month for recurring

                        // Prepare invoice data
                        $invoice_data = [
                            'clientid' => $patient->client_id,
                            'date' => date('Y-m-d'),
                            'duedate' => date('Y-m-d', strtotime('+7 days')), // 7 days to pay
                            'currency' => get_base_currency()->id,
                            'adminnote' => 'Programme: ' . $data['program_name'] . ' (ID: ' . $program_id . ')',
                            'newitems' => [
                                [
                                    'description' => $service->description . ' - ' . $data['duration_months'] . ' mois',
                                    'long_description' => $data['payment_mode'] == 'one_time'
                                        ? 'Paiement unique pour ' . $data['duration_months'] . ' mois'
                                        : 'Paiement mensuel (Mois 1/' . $data['duration_months'] . ')',
                                    'qty' => 1,
                                    'rate' => $invoice_amount,
                                    'taxname' => []
                                ]
                            ],
                            'tags' => ['programme_' . $program_id]
                        ];

                        // Create invoice
                        $invoice_id = $this->invoices_model->add($invoice_data);

                        if ($invoice_id) {
                            log_activity('PROGRAM CREATE - Invoice created: #' . $invoice_id . ' for ' . $invoice_amount . ' FCFA');

                            // Verify and fix invoice if needed (like in portal subscribe_service)
                            $created_invoice = $this->db->get_where(db_prefix() . 'invoices', ['id' => $invoice_id])->row();

                            if (empty($created_invoice->total) || $created_invoice->total == 0) {
                                $this->db->where('id', $invoice_id);
                                $this->db->update(db_prefix() . 'invoices', [
                                    'subtotal' => $invoice_amount,
                                    'total' => $invoice_amount
                                ]);
                                log_activity('PROGRAM CREATE - Fixed invoice totals');
                            }

                            if ($created_invoice->status != 1) {
                                $this->db->where('id', $invoice_id);
                                $this->db->update(db_prefix() . 'invoices', ['status' => 1]);
                                log_activity('PROGRAM CREATE - Set invoice status to Unpaid');
                            }

                            if (empty($created_invoice->number) || $created_invoice->number == 0) {
                                $next_number = get_option('next_invoice_number');
                                if (empty($next_number)) {
                                    $last_invoice = $this->db->select('number')
                                        ->from(db_prefix() . 'invoices')
                                        ->where('id !=', $invoice_id)
                                        ->order_by('CAST(number AS UNSIGNED)', 'DESC')
                                        ->limit(1)
                                        ->get()
                                        ->row();

                                    $next_number = $last_invoice && is_numeric($last_invoice->number) ? intval($last_invoice->number) + 1 : 1;
                                }

                                $this->db->where('id', $invoice_id);
                                $this->db->update(db_prefix() . 'invoices', ['number' => $next_number]);

                                update_option('next_invoice_number', $next_number + 1);
                                log_activity('PROGRAM CREATE - Fixed invoice number: ' . $next_number);
                            }

                            // Update program with billing status
                            $this->db->where('id', $program_id);
                            $this->db->update(db_prefix() . 'dietic_programs', [
                                'billing_status' => 'pending', // Will be active after payment
                                'last_billing_date' => date('Y-m-d')
                            ]);

                            set_alert('success', 'Programme créé avec succès. Facture #' . $invoice_id . ' générée (' . number_format($invoice_amount, 0, ',', ' ') . ' FCFA).');

                        } else {
                            log_activity('PROGRAM CREATE - ERROR: Failed to create invoice');
                            set_alert('warning', 'Programme créé mais erreur lors de la création de la facture.');
                        }

                    } catch (Exception $e) {
                        log_activity('PROGRAM CREATE - Invoice creation error: ' . $e->getMessage());
                        set_alert('warning', 'Programme créé mais erreur lors de la création de la facture: ' . $e->getMessage());
                    }
                }

                // ============================================
                // SEND NOTIFICATIONS (SMS, WhatsApp, Email, Push)
                // ============================================
                try {
                    if (isset($data['patient_id']) && $this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
                        $this->load->model('dietetic/dietetic_notifications_model');

                        // Get program info
                        $program = $this->dietetic_programs_model->get($program_id);

                        // Get dietitian info
                        $dietitian_id = $data['dietitian_id'] ?? get_staff_user_id();
                        $dietitian = $this->staff_model->get($dietitian_id);
                        $dietitian_name = $dietitian ? ($dietitian->firstname . ' ' . $dietitian->lastname) : 'Votre diététicien';

                        // Prepare billing data for notification
                        $billing_data = [
                            'program_id' => $program_id,
                            'duration_months' => $data['duration_months'] ?? null,
                            'total_price' => $data['total_price'] ?? 0,
                            'payment_mode' => $data['payment_mode'] ?? null,
                            'start_date' => $program->start_date ?? null,
                            'end_date' => $program->end_date ?? null,
                            'invoice_id' => $invoice_id ?? null
                        ];

                        // Send comprehensive notification via all channels
                        $result = $this->dietetic_notifications_model->notify_program_created_with_invoice(
                            $data['patient_id'],
                            $program->name,
                            $dietitian_name,
                            $billing_data
                        );

                        if ($result) {
                            log_activity('PROGRAM CREATE - All notifications sent successfully');
                        } else {
                            log_activity('PROGRAM CREATE - Notifications may not have been sent (preferences disabled or error)');
                        }
                    }
                } catch (Exception $e) {
                    log_activity('PROGRAM CREATE - Notification error: ' . $e->getMessage());
                }

                redirect(admin_url('dietetic/programs/view/' . $program_id));
            } else {
                set_alert('danger', _l('dietetic_error_add_failed'));
            }
        }

        $data['title'] = _l('dietetic_new_program');
        $data['patients'] = $this->dietetic_patients_model->get_all();
        $data['staff'] = $this->staff_model->get();

        // Get available services from Perfex items (for billing)
        $this->db->select('i.*, ig.name as group_name');
        $this->db->from(db_prefix() . 'items i');
        $this->db->join(db_prefix() . 'items_groups ig', 'ig.id = i.group_id', 'left');
        $this->db->where('ig.name', 'Services');
        $this->db->order_by('i.description', 'ASC');
        $data['services'] = $this->db->get()->result();

        // Auto-calculate nutritional objectives from anamnesis data if patient_id is provided
        $data['calculated_objectives'] = null;
        $patient_id = $this->input->get('patient_id');

        if ($patient_id) {
            $patient = $this->dietetic_patients_model->get($patient_id);

            if ($patient) {
                $this->load->library('dietetic/Dietetic_nutrition_calculator');

                // Get patient latest measurement for current weight
                $this->db->where('patient_id', $patient->id);
                $this->db->order_by('measurement_date', 'DESC');
                $this->db->limit(1);
                $latest_measurement = $this->db->get(db_prefix() . 'dietic_measurements')->row();

                $current_weight = $latest_measurement ? $latest_measurement->weight : null;

                // Calculate age from birth_date
                $age = null;
                if ($patient->birth_date) {
                    $birthdate = new DateTime($patient->birth_date);
                    $today = new DateTime();
                    $age = $birthdate->diff($today)->y;
                }

                // Only calculate if we have the minimum required data
                if ($current_weight && $patient->height && $age) {
                    // Map activity level from patient data to calculator constants
                    $activity_map = [
                        'sedentary' => Dietetic_nutrition_calculator::ACTIVITY_SEDENTARY,
                        'light' => Dietetic_nutrition_calculator::ACTIVITY_LIGHT,
                        'moderate' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
                        'active' => Dietetic_nutrition_calculator::ACTIVITY_ACTIVE,
                        'very_active' => Dietetic_nutrition_calculator::ACTIVITY_VERY_ACTIVE
                    ];

                    $activity_level = $activity_map[$patient->activity_level] ?? Dietetic_nutrition_calculator::ACTIVITY_MODERATE;

                    // Default to maintenance for new programs
                    $goal = Dietetic_nutrition_calculator::GOAL_MAINTENANCE;

                    $patient_data = [
                        'weight' => $current_weight,
                        'height' => $patient->height,
                        'age' => $age,
                        'gender' => $patient->gender,
                        'activity_level' => $activity_level,
                        'goal' => $goal,
                        'waist' => $patient->waist_circumference ?? null,
                        'neck' => $patient->neck_circumference ?? null,
                        'hip' => $patient->hip_circumference ?? null
                    ];

                    $data['calculated_objectives'] = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_data);
                }
            }
        }

        $this->load->view('admin/programs/form', $data);
    }

    /**
     * Edit program
     *
     * @param int $id
     */
    public function edit($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['program'] = $this->dietetic_programs_model->get($id);

        if (!$data['program']) {
            show_404();
        }

        if ($this->input->post()) {
            $update_data = $this->input->post();

            // Check if status changed for logging
            $old_status = $data['program']->status;
            $new_status = isset($update_data['status']) ? $update_data['status'] : $old_status;
            $status_changed = ($old_status != $new_status);

            if ($this->dietetic_programs_model->update($id, $update_data)) {
                // Log program update
                log_activity('Programme modifié : "' . $data['program']->program_name . '" (ID: ' . $id . ')');

                // Log status change if applicable
                if ($status_changed) {
                    $status_labels = [
                        'active' => 'Actif',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé'
                    ];
                    $old_label = isset($status_labels[$old_status]) ? $status_labels[$old_status] : $old_status;
                    $new_label = isset($status_labels[$new_status]) ? $status_labels[$new_status] : $new_status;

                    log_activity('Changement de statut du programme "' . $data['program']->program_name . '" (ID: ' . $id . ') : ' . $old_label . ' → ' . $new_label);
                }

                // Send notification to patient
                try {
                    if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences') && $data['program']->patient_id) {
                        $this->load->model('dietetic/dietetic_notifications_model');

                        // Get updated program info
                        $program = $this->dietetic_programs_model->get($id);

                        // Get dietitian info
                        $dietitian_id = $program->dietitian_id;
                        $dietitian = $this->staff_model->get($dietitian_id);
                        $dietitian_name = $dietitian ? ($dietitian->firstname . ' ' . $dietitian->lastname) : 'Votre diététicien';

                        // Send notification
                        $this->dietetic_notifications_model->notify_program_updated(
                            $program->patient_id,
                            $program->name,
                            $dietitian_name
                        );
                    }
                } catch (Exception $e) {
                    log_activity('Program update notification error: ' . $e->getMessage());
                }

                set_alert('success', _l('updated_successfully'));
                redirect(admin_url('dietetic/programs/view/' . $id));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }
        }

        $data['title'] = _l('dietetic_edit_program');
        $data['patients'] = $this->dietetic_patients_model->get_all();
        $data['staff'] = $this->staff_model->get();

        // Get available services from Perfex items (for billing)
        $this->db->select('i.*, ig.name as group_name');
        $this->db->from(db_prefix() . 'items i');
        $this->db->join(db_prefix() . 'items_groups ig', 'ig.id = i.group_id', 'left');
        $this->db->where('ig.name', 'Services');
        $this->db->order_by('i.description', 'ASC');
        $data['services'] = $this->db->get()->result();

        // Auto-calculate nutritional objectives from anamnesis data
        $data['calculated_objectives'] = null;
        $patient = $this->dietetic_patients_model->get($data['program']->patient_id);

        if ($patient) {
            $this->load->library('dietetic/Dietetic_nutrition_calculator');

            // Get patient latest measurement for current weight
            $this->db->where('patient_id', $patient->id);
            $this->db->order_by('measurement_date', 'DESC');
            $this->db->limit(1);
            $latest_measurement = $this->db->get(db_prefix() . 'dietic_measurements')->row();

            $current_weight = $latest_measurement ? $latest_measurement->weight : null;

            // Calculate age from birth_date
            $age = null;
            if ($patient->birth_date) {
                $birthdate = new DateTime($patient->birth_date);
                $today = new DateTime();
                $age = $birthdate->diff($today)->y;
            }

            // Only calculate if we have the minimum required data
            if ($current_weight && $patient->height && $age) {
                // Map activity level from patient data to calculator constants
                $activity_map = [
                    'sedentary' => Dietetic_nutrition_calculator::ACTIVITY_SEDENTARY,
                    'light' => Dietetic_nutrition_calculator::ACTIVITY_LIGHT,
                    'moderate' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
                    'active' => Dietetic_nutrition_calculator::ACTIVITY_ACTIVE,
                    'very_active' => Dietetic_nutrition_calculator::ACTIVITY_VERY_ACTIVE
                ];

                $activity_level = $activity_map[$patient->activity_level] ?? Dietetic_nutrition_calculator::ACTIVITY_MODERATE;

                // Determine goal from program objective or default to maintenance
                $goal = Dietetic_nutrition_calculator::GOAL_MAINTENANCE;
                if ($data['program']->objective) {
                    $objective_lower = strtolower($data['program']->objective);
                    if (strpos($objective_lower, 'perte') !== false || strpos($objective_lower, 'perd') !== false || strpos($objective_lower, 'maigrir') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS;
                    } elseif (strpos($objective_lower, 'prise') !== false || strpos($objective_lower, 'gagn') !== false || strpos($objective_lower, 'gross') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_GAIN;
                    } elseif (strpos($objective_lower, 'muscle') !== false || strpos($objective_lower, 'muscul') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_MUSCLE_GAIN;
                    }
                }

                $patient_data = [
                    'weight' => $current_weight,
                    'height' => $patient->height,
                    'age' => $age,
                    'gender' => $patient->gender,
                    'activity_level' => $activity_level,
                    'goal' => $goal,
                    'waist' => $patient->waist_circumference ?? null,
                    'neck' => $patient->neck_circumference ?? null,
                    'hip' => $patient->hip_circumference ?? null
                ];

                $data['calculated_objectives'] = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_data);
            }
        }

        $this->load->view('admin/programs/form', $data);
    }

    /**
     * Delete program
     *
     * @param int $id
     */
    public function delete($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        // Get program info before deletion for logging
        $program = $this->dietetic_programs_model->get($id);
        $program_name = $program ? $program->program_name : 'Programme #' . $id;

        // Safely get patient name
        $patient_name = 'N/A';
        if ($program && $program->patient_id) {
            $patient = $this->dietetic_patients_model->get($program->patient_id);
            if ($patient && isset($patient->client) && isset($patient->client->company)) {
                $patient_name = $patient->client->company;
            }
        }

        if ($this->dietetic_programs_model->delete($id)) {
            // Log successful deletion
            log_activity('Programme supprimé : "' . $program_name . '" (ID: ' . $id . ') - Patient: ' . $patient_name);

            echo json_encode(['success' => true, 'message' => _l('deleted')]);
        } else {
            // Log failed deletion attempt
            log_activity('Échec de suppression du programme : "' . $program_name . '" (ID: ' . $id . ')');

            echo json_encode(['success' => false, 'message' => _l('dietetic_error_delete_failed')]);
        }
    }

    /**
     * View meal plan
     *
     * @param int $id Meal plan ID
     */
    public function meal_plan($id)
    {
        $data['meal_plan'] = $this->dietetic_meal_plans_model->get($id);

        if (!$data['meal_plan']) {
            show_404();
        }

        $data['program'] = $this->dietetic_programs_model->get($data['meal_plan']->program_id);
        $data['patient'] = $this->dietetic_patients_model->get($data['program']->patient_id);
        $data['meals_by_day'] = $this->dietetic_meal_plans_model->get_meals_by_day($id);
        $data['nutrition_totals'] = $this->dietetic_meal_plans_model->calculate_plan_nutrition($id);
        $data['title'] = $data['meal_plan']->plan_name;

        $this->load->view('admin/programs/meal_plan_view', $data);
    }

    /**
     * Create meal plan for program
     *
     * @param int $program_id
     */
    public function create_meal_plan($program_id)
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        $data['program'] = $this->dietetic_programs_model->get($program_id);

        if (!$data['program']) {
            show_404();
        }

        if ($this->input->post()) {
            $plan_data = $this->input->post();
            $plan_data['program_id'] = $program_id;

            $plan_id = $this->dietetic_meal_plans_model->add($plan_data);

            if ($plan_id) {
                set_alert('success', _l('added_successfully'));
                redirect(admin_url('dietetic/programs/meal_plan/' . $plan_id));
            } else {
                set_alert('danger', _l('dietetic_error_add_failed'));
            }
        }

        $data['title'] = _l('dietetic_new_meal_plan');
        $data['foods'] = $this->dietetic_foods_model->get_active();

        $this->load->view('admin/programs/meal_plan_form', $data);
    }

    /**
     * Edit meal plan
     *
     * @param int $id
     */
    public function edit_meal_plan($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['meal_plan'] = $this->dietetic_meal_plans_model->get($id);

        if (!$data['meal_plan']) {
            show_404();
        }

        $data['program'] = $this->dietetic_programs_model->get($data['meal_plan']->program_id);

        if ($this->input->post()) {
            $update_data = $this->input->post();

            if ($this->dietetic_meal_plans_model->update($id, $update_data)) {
                set_alert('success', _l('updated_successfully'));
                redirect(admin_url('dietetic/programs/meal_plan/' . $id));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }
        }

        $data['title'] = _l('dietetic_edit_meal_plan');
        $data['foods'] = $this->dietetic_foods_model->get_active();
        $data['meals_by_day'] = $this->dietetic_meal_plans_model->get_meals_by_day($id);

        $this->load->view('admin/programs/meal_plan_form', $data);
    }

    /**
     * Add meal via AJAX
     */
    public function add_meal()
    {
        if (!dietetic_has_permission('create')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $meal_id = $this->dietetic_meal_plans_model->add_meal($data);

            if ($meal_id) {
                echo json_encode(['success' => true, 'meal_id' => $meal_id, 'message' => _l('added_successfully')]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('dietetic_error_add_failed')]);
            }
        }
    }

    /**
     * Add food to meal via AJAX
     */
    public function add_food_to_meal()
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $id = $this->dietetic_meal_plans_model->add_food_to_meal($data);

            if ($id) {
                echo json_encode(['success' => true, 'id' => $id, 'message' => _l('added_successfully')]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('dietetic_error_add_failed')]);
            }
        }
    }

    /**
     * Remove food from meal via AJAX
     *
     * @param int $id
     */
    public function remove_food_from_meal($id)
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->dietetic_meal_plans_model->remove_food_from_meal($id)) {
            echo json_encode(['success' => true, 'message' => _l('deleted')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('dietetic_error_delete_failed')]);
        }
    }

    /**
     * Meal management - Create/Edit meal (dedicated pages, not AJAX)
     */
    public function meal($action = 'create', $id = null)
    {
        if ($action == 'create') {
            if (!dietetic_has_permission('create')) {
                access_denied('dietetic');
            }

            $meal_plan_id = $this->input->get('meal_plan_id') ?: $this->input->post('meal_plan_id');

            if (!$meal_plan_id) {
                set_alert('danger', 'Meal plan ID required');
                redirect(admin_url('dietetic/programs'));
                return;
            }

            $data['meal_plan'] = $this->dietetic_meal_plans_model->get($meal_plan_id);
            if (!$data['meal_plan']) {
                show_404();
            }

            $data['program'] = $this->dietetic_programs_model->get($data['meal_plan']->program_id);

            // Load all recipes for selection (dietitians can use all recipes from library)
            $data['recipes'] = $this->dietetic_recipes_model->get_all();

            if ($this->input->post()) {
                // Enable error display for debugging
                ini_set('display_errors', 1);
                error_reporting(E_ALL);

                try {
                    // DEBUG: Log all POST data
                    log_message('debug', 'MEAL CREATE - POST Data: ' . print_r($this->input->post(), true));

                    $recipe_id = $this->input->post('recipe_id');
                    log_message('debug', 'MEAL CREATE - Recipe ID: ' . ($recipe_id ? $recipe_id : 'NONE'));

                    // If a recipe is selected, get recipe data
                    $recipe = null;
                    if ($recipe_id) {
                        $recipe = $this->dietetic_recipes_model->get($recipe_id);
                        log_message('debug', 'MEAL CREATE - Recipe loaded: ' . ($recipe ? 'YES' : 'NO'));
                        if ($recipe) {
                            log_message('debug', 'MEAL CREATE - Recipe details: ' . print_r($recipe, true));
                        }

                        if (!$recipe) {
                            log_message('error', 'MEAL CREATE - Recipe not found: ' . $recipe_id);
                            set_alert('danger', 'Recipe not found');
                            redirect($_SERVER['HTTP_REFERER']);
                            return;
                        }
                    }

                    // Map form fields to database columns
                    // Build instructions from recipe if selected
                    $instructions = $this->input->post('instructions');
                    if ($recipe_id && $recipe) {
                        // Join all recipe instructions with line breaks for proper numbering
                        if (!empty($recipe->instructions)) {
                            $instruction_lines = [];
                            foreach ($recipe->instructions as $inst) {
                                $instruction_lines[] = $inst->instruction;
                            }
                            $instructions = implode("\n", $instruction_lines);
                        } else {
                            // Fallback to description if no structured instructions
                            $instructions = $recipe->description;
                        }
                    }

                    $meal_data = [
                        'meal_plan_id' => $this->input->post('meal_plan_id'),
                        'day_of_week' => $this->input->post('day_number'), // Map day_number to day_of_week
                        'meal_type' => $this->input->post('meal_type'),
                        'meal_name' => $recipe_id && $recipe ? $recipe->name : $this->input->post('meal_name'),
                        'meal_time' => $this->input->post('meal_time'),
                        'instructions' => $instructions,
                        'display_order' => 0
                    ];

                    log_message('debug', 'MEAL CREATE - Meal data to insert: ' . print_r($meal_data, true));

                    $meal_id = $this->dietetic_meal_plans_model->add_meal($meal_data);
                    log_message('debug', 'MEAL CREATE - Meal ID created: ' . ($meal_id ? $meal_id : 'FAILED'));

                    if ($meal_id) {
                        // If a recipe was selected, copy ingredients to meal
                        if ($recipe_id && isset($recipe) && $recipe && !empty($recipe->ingredients)) {
                            log_message('debug', 'MEAL CREATE - Processing ' . count($recipe->ingredients) . ' ingredients');

                            $ingredient_count = 0;
                            foreach ($recipe->ingredients as $ingredient) {
                                $ingredient_count++;
                                log_message('debug', 'MEAL CREATE - Ingredient #' . $ingredient_count . ': ' . print_r($ingredient, true));

                                // Get food by name - search in food database
                                $this->db->where('food_name', $ingredient->ingredient_name);
                                $food = $this->db->get(db_prefix() . 'dietic_foods')->row();

                                log_message('debug', 'MEAL CREATE - Food found for "' . $ingredient->ingredient_name . '": ' . ($food ? 'YES (ID: ' . $food->id . ')' : 'NO'));

                                if ($food) {
                                    // Use ingredient quantity and unit from recipe
                                    $meal_food_data = [
                                        'meal_id' => $meal_id,
                                        'food_id' => $food->id,
                                        'quantity' => $ingredient->quantity ?? 100, // Default 100g if not specified
                                        'unit' => $ingredient->unit ?? 'g', // Default to grams if not specified
                                        'display_order' => isset($ingredient->order) ? $ingredient->order : 0
                                    ];

                                    log_message('debug', 'MEAL CREATE - Adding meal_food: ' . print_r($meal_food_data, true));

                                    $result = $this->dietetic_meal_plans_model->add_food_to_meal($meal_food_data);
                                    log_message('debug', 'MEAL CREATE - Meal food added: ' . ($result ? 'SUCCESS' : 'FAILED'));
                                } else {
                                    log_message('warning', 'MEAL CREATE - Food not found in database: ' . $ingredient->ingredient_name);
                                }
                            }

                            log_message('debug', 'MEAL CREATE - Finished processing ingredients');
                        } else {
                            log_message('debug', 'MEAL CREATE - No ingredients to process (recipe_id: ' . ($recipe_id ? $recipe_id : 'none') . ')');
                        }

                        log_message('debug', 'MEAL CREATE - SUCCESS - Redirecting to meal edit page');
                        set_alert('success', 'Meal created successfully' . ($recipe_id ? ' from recipe' : ''));
                        redirect(admin_url('dietetic/programs/meal/edit/' . $meal_id));
                    } else {
                        log_message('error', 'MEAL CREATE - Failed to create meal in database');
                        set_alert('danger', 'Failed to create meal');
                    }

                } catch (Exception $e) {
                    // Log the full exception
                    log_message('error', 'MEAL CREATE - EXCEPTION: ' . $e->getMessage());
                    log_message('error', 'MEAL CREATE - Stack trace: ' . $e->getTraceAsString());
                    log_message('error', 'MEAL CREATE - File: ' . $e->getFile() . ' Line: ' . $e->getLine());

                    // Show detailed error to user for debugging
                    $error_msg = 'Error creating meal: ' . $e->getMessage() . ' (File: ' . basename($e->getFile()) . ':' . $e->getLine() . ')';
                    set_alert('danger', $error_msg);

                    // Also output error directly for debugging
                    echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px;">';
                    echo '<h4>DEBUG - Error Details:</h4>';
                    echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
                    echo '<p><strong>Trace:</strong></p>';
                    echo '<pre style="background: white; padding: 10px; overflow: auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                    echo '</div>';
                    exit; // Stop execution to show the error
                }
            }

            $data['title'] = 'Add Meal';
            $this->load->view('admin/programs/meal_form', $data);

        } elseif ($action == 'edit') {
            if (!dietetic_has_permission('edit')) {
                access_denied('dietetic');
            }

            $data['meal'] = $this->dietetic_meal_plans_model->get_meal($id);
            if (!$data['meal']) {
                show_404();
            }

            $data['meal_plan'] = $this->dietetic_meal_plans_model->get($data['meal']->meal_plan_id);
            $data['program'] = $this->dietetic_programs_model->get($data['meal_plan']->program_id);
            $data['meal_foods'] = $this->dietetic_meal_plans_model->get_meal_foods($id);

            if ($this->input->post()) {
                // Map form fields to database columns
                $meal_data = [
                    'day_of_week' => $this->input->post('day_number'), // Map day_number to day_of_week
                    'meal_type' => $this->input->post('meal_type'),
                    'meal_name' => $this->input->post('meal_name'),
                    'meal_time' => $this->input->post('meal_time'),
                    'instructions' => $this->input->post('instructions')
                ];

                if ($this->dietetic_meal_plans_model->update_meal($id, $meal_data)) {
                    set_alert('success', 'Meal updated successfully');
                } else {
                    set_alert('danger', 'Failed to update meal');
                }
                redirect(admin_url('dietetic/programs/meal/edit/' . $id));
            }

            $data['title'] = 'Edit Meal';
            $this->load->view('admin/programs/meal_form', $data);
        }
    }

    /**
     * Meal food management - Add/Edit food in meal
     */
    public function meal_food($action = 'create', $id = null)
    {
        if ($action == 'create') {
            if (!dietetic_has_permission('create')) {
                access_denied('dietetic');
            }

            $meal_id = $this->input->get('meal_id') ?: $this->input->post('meal_id');

            if (!$meal_id) {
                set_alert('danger', 'Meal ID required');
                redirect(admin_url('dietetic/programs'));
                return;
            }

            $data['meal'] = $this->dietetic_meal_plans_model->get_meal($meal_id);
            if (!$data['meal']) {
                show_404();
            }

            $data['foods'] = $this->dietetic_foods_model->get_all();
            $data['days'] = ['1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday'];

            if ($this->input->post()) {
                $food_data = $this->input->post();
                $food_id = $this->dietetic_meal_plans_model->add_food_to_meal($food_data);

                if ($food_id) {
                    set_alert('success', 'Food added to meal successfully');
                    redirect(admin_url('dietetic/programs/meal/edit/' . $meal_id));
                } else {
                    set_alert('danger', 'Failed to add food');
                }
            }

            $data['title'] = 'Add Food to Meal';
            $this->load->view('admin/programs/meal_food_form', $data);

        } elseif ($action == 'edit') {
            if (!dietetic_has_permission('edit')) {
                access_denied('dietetic');
            }

            $data['meal_food'] = $this->dietetic_meal_plans_model->get_meal_food($id);
            if (!$data['meal_food']) {
                show_404();
            }

            $data['meal'] = $this->dietetic_meal_plans_model->get_meal($data['meal_food']->meal_id);
            $data['foods'] = $this->dietetic_foods_model->get_all();
            $data['days'] = ['1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday'];

            if ($this->input->post()) {
                $food_data = $this->input->post();
                if ($this->dietetic_meal_plans_model->update_food_in_meal($id, $food_data)) {
                    set_alert('success', 'Food updated successfully');
                } else {
                    set_alert('danger', 'Failed to update food');
                }
                redirect(admin_url('dietetic/programs/meal/edit/' . $data['meal']->id));
            }

            $data['title'] = 'Edit Food in Meal';
            $this->load->view('admin/programs/meal_food_form', $data);

        } elseif ($action == 'delete') {
            if (!dietetic_has_permission('delete')) {
                access_denied('dietetic');
            }

            $meal_food = $this->dietetic_meal_plans_model->get_meal_food($id);
            if ($meal_food) {
                if ($this->dietetic_meal_plans_model->remove_food_from_meal($id)) {
                    set_alert('success', 'Food removed from meal');
                } else {
                    set_alert('danger', 'Failed to remove food');
                }
                redirect(admin_url('dietetic/programs/meal/edit/' . $meal_food->meal_id));
            } else {
                show_404();
            }
        }
    }

    /**
     * Generate PDF for meal plan
     *
     * @param int $id
     */
    public function generate_pdf($id)
    {
        $this->load->library('dietetic/dietetic_pdf');

        $meal_plan = $this->dietetic_meal_plans_model->get($id);

        if (!$meal_plan) {
            show_404();
        }

        $program = $this->dietetic_programs_model->get($meal_plan->program_id);
        $patient = $this->dietetic_patients_model->get($program->patient_id);

        $pdf_data = [
            'meal_plan' => $meal_plan,
            'program'   => $program,
            'patient'   => $patient,
            'meals_by_day' => $this->dietetic_meal_plans_model->get_meals_by_day($id),
        ];

        $this->dietetic_pdf->generate_meal_plan_pdf($pdf_data);
    }
}
