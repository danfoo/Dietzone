<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$active_page = 'book_appointment';
$page_title = 'Diagnostic - Prise de RDV';
$this->load->view('portal/includes/portal_header');
?>

<div class="page-header-mobile">
    <h1>
        <i class="fa fa-stethoscope"></i>
        Diagnostic Prise de Rendez-vous
    </h1>
    <p>Vérification de votre configuration</p>
</div>

<div style="background: white; border-radius: 16px; padding: 24px; margin-bottom: 24px;">
    <h3>Informations Patient</h3>
    <ul>
        <li><strong>ID Patient:</strong> <?php echo $patient->id; ?></li>
        <li><strong>Client ID:</strong> <?php echo $patient->client_id; ?></li>
    </ul>

    <hr>

    <h3>Diététiciens Assignés (<?php echo count($dietitians); ?>)</h3>

    <?php if (empty($dietitians)): ?>
        <div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 15px 0;">
            <h4 style="color: #d32f2f; margin-top: 0;">⚠ Aucun diététicien assigné</h4>
            <p>Vous n'avez pas encore de diététicien assigné. C'est pourquoi vous ne pouvez pas voir de dates disponibles.</p>
            <p><strong>Solution:</strong> Contactez l'administration pour qu'un diététicien vous soit assigné.</p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 10px; text-align: left;">Diététicien</th>
                    <th style="padding: 10px; text-align: left;">Principal</th>
                    <th style="padding: 10px; text-align: left;">Email</th>
                    <th style="padding: 10px; text-align: left;">Disponibilités</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dietitians as $dietitian): ?>
                    <?php
                    // Check if this dietitian has availabilities
                    $this->db->where('dietitian_id', $dietitian->dietitian_id);
                    $this->db->where('is_active', 1);
                    $avail_count = $this->db->count_all_results(db_prefix() . 'dietic_dietitian_availability');
                    ?>
                    <tr style="border-bottom: 1px solid #e0e0e0;">
                        <td style="padding: 10px;">
                            <?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>
                        </td>
                        <td style="padding: 10px;">
                            <?php if ($dietitian->is_primary): ?>
                                <span style="background: #4caf50; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px;">OUI</span>
                            <?php else: ?>
                                <span style="color: #999;">Non</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($dietitian->email); ?></td>
                        <td style="padding: 10px;">
                            <?php if ($avail_count > 0): ?>
                                <span style="color: #4caf50; font-weight: bold;"><?php echo $avail_count; ?> créneaux actifs</span>
                            <?php else: ?>
                                <span style="color: #f44336; font-weight: bold;">Aucun créneau</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        // Check if any dietitian has availabilities
        $has_any_availability = false;
        foreach ($dietitians as $d) {
            $this->db->where('dietitian_id', $d->dietitian_id);
            $this->db->where('is_active', 1);
            if ($this->db->count_all_results(db_prefix() . 'dietic_dietitian_availability') > 0) {
                $has_any_availability = true;
                break;
            }
        }
        ?>

        <?php if (!$has_any_availability): ?>
            <div style="background: #fff3e0; padding: 15px; border-left: 4px solid #ff9800; margin: 15px 0;">
                <h4 style="color: #e65100; margin-top: 0;">⚠ Aucune disponibilité configurée</h4>
                <p>Vos diététiciens n'ont pas encore de créneaux de disponibilité configurés.</p>
                <p><strong>Solution:</strong> Contactez l'administration pour qu'ils configurent les disponibilités.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <hr>

    <h3>Types de Consultation (<?php echo count($consultation_types); ?>)</h3>
    <?php if (!empty($consultation_types)): ?>
        <ul>
            <?php foreach ($consultation_types as $type): ?>
                <li><?php echo htmlspecialchars($type->name); ?> - <?php echo $type->duration; ?> minutes</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336;">
            <p style="margin: 0;">Aucun type de consultation configuré.</p>
        </div>
    <?php endif; ?>

    <hr>

    <div style="display: flex; gap: 12px;">
        <a href="<?php echo site_url('dietetic/portal/book_appointment'); ?>" class="btn" style="flex: 1; padding: 12px; background: #01807B; color: white; text-align: center; border-radius: 8px; text-decoration: none;">
            <i class="fa fa-arrow-left"></i> Retour à la Réservation
        </a>
        <a href="<?php echo site_url('dietetic/portal/portal_dashboard'); ?>" class="btn" style="flex: 1; padding: 12px; background: #e0e0e0; color: #333; text-align: center; border-radius: 8px; text-decoration: none;">
            <i class="fa fa-home"></i> Tableau de Bord
        </a>
    </div>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
