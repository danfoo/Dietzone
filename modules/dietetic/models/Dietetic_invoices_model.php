<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_invoices_model extends App_Model
{
    private $table = 'dietic_invoices';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get invoice by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->select('i.*, ' .
            'c.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name, st.email as dietitian_email, ' .
            'sp.name as plan_name, sp.name_fr as plan_name_fr, ' .
            's.referral_source');
        $this->db->from(db_prefix() . $this->table . ' i');
        $this->db->join(db_prefix() . 'dietic_subscriptions s', 's.id = i.subscription_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = i.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = i.dietitian_id', 'left');
        $this->db->join(db_prefix() . 'dietic_service_plans sp', 'sp.id = s.service_plan_id', 'left');
        $this->db->where('i.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Get invoice by invoice number
     *
     * @param string $invoice_number
     * @return object|null
     */
    public function get_by_number($invoice_number)
    {
        $this->db->where('invoice_number', $invoice_number);
        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Get all invoices
     *
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_all($where = [], $limit = null, $offset = null)
    {
        $this->db->select('i.*, ' .
            'c.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name, ' .
            'sp.name as plan_name');
        $this->db->from(db_prefix() . $this->table . ' i');
        $this->db->join(db_prefix() . 'dietic_subscriptions s', 's.id = i.subscription_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = i.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = i.dietitian_id', 'left');
        $this->db->join(db_prefix() . 'dietic_service_plans sp', 'sp.id = s.service_plan_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('i.dietitian_id', get_staff_user_id());
        }

        $this->db->order_by('i.issue_date', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Get invoices by subscription
     *
     * @param int $subscription_id
     * @return array
     */
    public function get_by_subscription($subscription_id)
    {
        return $this->get_all(['i.subscription_id' => $subscription_id]);
    }

    /**
     * Get invoices by patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_by_patient($patient_id)
    {
        return $this->get_all(['i.patient_id' => $patient_id]);
    }

    /**
     * Count all invoices
     *
     * @param array $where
     * @return int
     */
    public function count_all($where = [])
    {
        $this->db->from(db_prefix() . $this->table . ' i');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('i.dietitian_id', get_staff_user_id());
        }

        return $this->db->count_all_results();
    }

    /**
     * Generate invoice number
     *
     * @return string
     */
    public function generate_invoice_number()
    {
        $year = date('Y');

        // Get or create sequence for current year
        $this->db->where('year', $year);
        $sequence = $this->db->get(db_prefix() . 'dietic_invoice_sequence')->row();

        if (!$sequence) {
            // Create new sequence for this year
            $this->db->insert(db_prefix() . 'dietic_invoice_sequence', [
                'year' => $year,
                'last_number' => 1
            ]);
            $number = 1;
        } else {
            // Increment sequence
            $number = $sequence->last_number + 1;
            $this->db->where('year', $year);
            $this->db->update(db_prefix() . 'dietic_invoice_sequence', [
                'last_number' => $number
            ]);
        }

        // Format: INV-2025-0001
        return 'INV-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Add new invoice
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        // Generate invoice number if not provided
        if (!isset($data['invoice_number'])) {
            $data['invoice_number'] = $this->generate_invoice_number();
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $invoice_id = $this->db->insert_id();

            log_activity('New Invoice Created [Number: ' . $data['invoice_number'] . ']');

            return $invoice_id;
        }

        return false;
    }

    /**
     * Update invoice
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $result = $this->db->update(db_prefix() . $this->table, $data);

        if ($result) {
            log_activity('Invoice Updated [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Mark invoice as paid
     *
     * @param int $id
     * @param string $payment_method
     * @return bool
     */
    public function mark_as_paid($id, $payment_method = null)
    {
        $data = [
            'status' => 'paid',
            'paid_date' => date('Y-m-d H:i:s'),
            'payment_method' => $payment_method
        ];

        $result = $this->update($id, $data);

        if ($result) {
            log_activity('Invoice Marked as Paid [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Send invoice to patient
     *
     * @param int $id
     * @return bool
     */
    public function send_invoice($id)
    {
        $invoice = $this->get($id);

        if (!$invoice || !$invoice->patient_email) {
            return false;
        }

        // Get patient email from client
        $this->db->select('email');
        $this->db->from(db_prefix() . 'dietic_patients p');
        $this->db->join(db_prefix() . 'contacts c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
        $this->db->where('p.id', $invoice->patient_id);
        $patient_contact = $this->db->get()->row();

        $email = $patient_contact && $patient_contact->email ? $patient_contact->email : $invoice->patient_email;

        if (!$email) {
            log_activity('Invoice Email Failed - No email found [Invoice: ' . $invoice->invoice_number . ']');
            return false;
        }

        // Generate PDF in memory
        $pdf_content = $this->generate_invoice_pdf($invoice);

        if (!$pdf_content) {
            log_activity('Invoice Email Failed - PDF generation failed [Invoice: ' . $invoice->invoice_number . ']');
            return false;
        }

        // Prepare email
        $patient_name = $invoice->patient_name ?: 'Client';
        $subject = 'Facture ' . $invoice->invoice_number . ' - ' . get_option('companyname');

        $message = $this->build_invoice_email_html($invoice, $patient_name);

        // Save PDF to temporary file
        $temp_dir = get_temp_dir();
        $pdf_filename = 'Facture_' . $invoice->invoice_number . '.pdf';
        $pdf_path = $temp_dir . $pdf_filename;
        file_put_contents($pdf_path, $pdf_content);

        // Send email with attachment
        try {
            $this->load->library('email');

            $config = [
                'mailtype' => 'html',
                'charset' => 'utf-8',
                'newline' => "\r\n"
            ];

            $this->email->initialize($config);
            $this->email->from(get_option('smtp_email'), get_option('companyname'));
            $this->email->to($email);
            $this->email->subject($subject);
            $this->email->message($message);
            $this->email->attach($pdf_path);

            $sent = $this->email->send();

            // Clean up temp file
            @unlink($pdf_path);

            if ($sent) {
                // Update status to sent
                $this->update($id, ['status' => 'sent', 'sent_at' => date('Y-m-d H:i:s')]);
                log_activity('Invoice Sent to Patient [Invoice: ' . $invoice->invoice_number . ', Email: ' . $email . ']');
                return true;
            } else {
                log_activity('Invoice Email Failed [Invoice: ' . $invoice->invoice_number . ', Email: ' . $email . ']: ' . $this->email->print_debugger());
                return false;
            }
        } catch (Exception $e) {
            @unlink($pdf_path);
            log_activity('Invoice Email Exception [Invoice: ' . $invoice->invoice_number . ']: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate invoice PDF content in memory
     *
     * @param object $invoice
     * @return string|bool PDF content or false on failure
     */
    private function generate_invoice_pdf($invoice)
    {
        try {
            // Load PDF library
            require_once(APPPATH . 'libraries/pdf/App_pdf.php');

            // Generate HTML from template
            $html = $this->load->view('dietetic/admin/invoices/pdf_template', [
                'invoice' => $invoice
            ], true);

            // Create PDF
            $pdf = new App_pdf();

            // Set document properties
            $pdf->SetTitle('Facture ' . $invoice->invoice_number);
            $pdf->SetAuthor(get_option('companyname'));
            $pdf->SetCreator('DIETZONE');
            $pdf->SetSubject('Facture de service');

            // Set margins
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 15);

            // Add page
            $pdf->AddPage();

            // Write HTML
            $pdf->writeHTML($html, true, false, true, false, '');

            // Return PDF as string
            return $pdf->Output('', 'S');
        } catch (Exception $e) {
            log_activity('PDF Generation Error [Invoice: ' . $invoice->invoice_number . ']: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build invoice email HTML
     *
     * @param object $invoice
     * @param string $patient_name
     * @return string
     */
    private function build_invoice_email_html($invoice, $patient_name)
    {
        $company_name = get_option('companyname');
        $company_logo = get_option('company_logo');
        $logo_url = $company_logo ? base_url('uploads/company/' . $company_logo) : '';

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .logo { max-width: 200px; margin-bottom: 20px; }
                .content { background: #f9f9f9; padding: 20px; border-radius: 5px; }
                .invoice-details { margin: 20px 0; }
                .invoice-details table { width: 100%; border-collapse: collapse; }
                .invoice-details td { padding: 8px; border-bottom: 1px solid #ddd; }
                .invoice-details td:first-child { font-weight: bold; width: 40%; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
                .button { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">';

        if ($logo_url) {
            $html .= '<img src="' . $logo_url . '" alt="' . $company_name . '" class="logo">';
        }

        $html .= '
                    <h2>' . $company_name . '</h2>
                </div>

                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($patient_name) . '</strong>,</p>

                    <p>Veuillez trouver ci-joint votre facture pour les services diététiques.</p>

                    <div class="invoice-details">
                        <table>
                            <tr>
                                <td>Numéro de facture</td>
                                <td>' . htmlspecialchars($invoice->invoice_number) . '</td>
                            </tr>
                            <tr>
                                <td>Date d\'émission</td>
                                <td>' . date('d/m/Y', strtotime($invoice->issue_date)) . '</td>
                            </tr>
                            <tr>
                                <td>Date d\'échéance</td>
                                <td>' . date('d/m/Y', strtotime($invoice->due_date)) . '</td>
                            </tr>
                            <tr>
                                <td>Montant total</td>
                                <td><strong>' . number_format($invoice->total_amount, 0, ',', ' ') . ' FCFA</strong></td>
                            </tr>
                        </table>
                    </div>

                    <p>La facture complète est jointe à cet email en format PDF.</p>

                    <p>Pour toute question concernant cette facture, n\'hésitez pas à nous contacter.</p>

                    <p>Cordialement,<br>
                    <strong>L\'équipe ' . $company_name . '</strong></p>
                </div>

                <div class="footer">
                    <p>' . get_option('company_address') . '</p>
                    <p>Email: ' . get_option('smtp_email') . ' | Tél: ' . get_option('company_phonenumber') . '</p>
                    <p>&copy; ' . date('Y') . ' ' . $company_name . '. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Check and update overdue invoices
     *
     * @return int Number of invoices updated
     */
    public function update_overdue_invoices()
    {
        $this->db->where('status', 'sent');
        $this->db->where('due_date <', date('Y-m-d'));

        $overdue_invoices = $this->db->get(db_prefix() . $this->table)->result();

        $count = 0;
        foreach ($overdue_invoices as $invoice) {
            if ($this->update($invoice->id, ['status' => 'overdue'])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get invoice statistics
     *
     * @param array $filters
     * @return array
     */
    public function get_statistics($filters = [])
    {
        $stats = [];

        // Total invoices
        $stats['total'] = $this->count_all();

        // By status
        $stats['draft'] = $this->count_all(['i.status' => 'draft']);
        $stats['sent'] = $this->count_all(['i.status' => 'sent']);
        $stats['paid'] = $this->count_all(['i.status' => 'paid']);
        $stats['overdue'] = $this->count_all(['i.status' => 'overdue']);
        $stats['cancelled'] = $this->count_all(['i.status' => 'cancelled']);

        // Total amounts
        $this->db->select_sum('total_amount');
        $this->db->from(db_prefix() . $this->table . ' i');
        $this->db->where('i.status', 'paid');
        if (!is_admin()) {
            $this->db->where('i.dietitian_id', get_staff_user_id());
        }
        $result = $this->db->get()->row();
        $stats['total_paid'] = $result ? (float)$result->total_amount : 0;

        // Outstanding amount
        $this->db->select_sum('total_amount');
        $this->db->from(db_prefix() . $this->table . ' i');
        $this->db->where_in('i.status', ['sent', 'overdue']);
        if (!is_admin()) {
            $this->db->where('i.dietitian_id', get_staff_user_id());
        }
        $result = $this->db->get()->row();
        $stats['outstanding'] = $result ? (float)$result->total_amount : 0;

        return $stats;
    }

    /**
     * Get revenue by period
     *
     * @param string $period (month, year)
     * @param int $dietitian_id
     * @return array
     */
    public function get_revenue_by_period($period = 'month', $dietitian_id = null)
    {
        $this->db->select('DATE_FORMAT(issue_date, "' . ($period == 'month' ? '%Y-%m' : '%Y') . '") as period, ' .
            'SUM(total_amount) as revenue, COUNT(*) as invoice_count');
        $this->db->from(db_prefix() . $this->table);
        $this->db->where('status', 'paid');

        if ($dietitian_id) {
            $this->db->where('dietitian_id', $dietitian_id);
        } elseif (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }

        $this->db->group_by('period');
        $this->db->order_by('period', 'DESC');
        $this->db->limit(12); // Last 12 periods

        return $this->db->get()->result();
    }

    /**
     * Delete invoice
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $invoice = $this->get($id);

        if (!$invoice) {
            return false;
        }

        // Only delete if draft or cancelled
        if (!in_array($invoice->status, ['draft', 'cancelled'])) {
            return false;
        }

        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . $this->table);

        if ($result) {
            log_activity('Invoice Deleted [Number: ' . $invoice->invoice_number . ']');
        }

        return $result;
    }
}
