<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <h4 class="pull-left"><?php echo $title; ?></h4>
                            <?php if (dietetic_has_permission('create')) { ?>
                                <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="btn btn-info pull-right">
                                    <i class="fa fa-plus"></i> <?php echo _l('dietetic_new_consultation'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <hr />

                        <div id="consultation_calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('consultation_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: {
            url: '<?php echo admin_url('dietetic/consultations/get_calendar_data'); ?>',
            failure: function() {
                alert('Failed to load consultations');
            }
        },
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        height: 'auto',
        navLinks: true,
        editable: false,
        dayMaxEvents: true
    });
    calendar.render();
});
</script>

<?php init_tail(); ?>
