<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'My Program'; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { padding-top: 20px; background: #f5f5f5; }
        .stat-box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-box h2 { margin: 10px 0; color: #333; }
        .stat-box p { color: #666; margin: 0; }
        .program-box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar-custom { background: #3498db; border: none; border-radius: 0; margin-bottom: 30px; }
        .navbar-custom .navbar-brand { color: white; }
        .navbar-custom .navbar-nav>li>a { color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="#"><i class="fa fa-heartbeat"></i> Dietetic Program</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo site_url('clients/profile'); ?>"><i class="fa fa-user"></i> Profile</a></li>
                <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2><i class="fa fa-heartbeat"></i> My Dietetic Program</h2>
        <hr>

        <!-- Stats -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-box">
                    <p>Current Weight</p>
                    <h2><?php echo isset($latest_measurement) && $latest_measurement ? $latest_measurement->weight . ' kg' : '-'; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <p>Target Weight</p>
                    <h2><?php echo isset($patient->target_weight) && $patient->target_weight ? $patient->target_weight . ' kg' : '-'; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <p>BMI</p>
                    <h2><?php echo isset($latest_measurement) && $latest_measurement && isset($latest_measurement->bmi) ? $latest_measurement->bmi : '-'; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <p>Progress</p>
                    <h2 class="<?php echo isset($weight_progress->weight_change) && $weight_progress->weight_change < 0 ? 'text-success' : ''; ?>">
                        <?php echo isset($weight_progress->weight_change) && $weight_progress->weight_change !== null ? ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1) . ' kg' : '-'; ?>
                    </h2>
                </div>
            </div>
        </div>

        <!-- Active Program -->
        <?php if (isset($active_program) && $active_program) { ?>
            <div class="program-box">
                <h3><i class="fa fa-check-circle text-success"></i> <?php echo htmlspecialchars($active_program->program_name); ?></h3>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Start Date:</strong> <?php echo date('d/m/Y', strtotime($active_program->start_date)); ?></p>
                        <?php if (isset($active_program->end_date) && $active_program->end_date) { ?>
                            <p><strong>End Date:</strong> <?php echo date('d/m/Y', strtotime($active_program->end_date)); ?></p>
                        <?php } ?>
                    </div>
                    <div class="col-md-6">
                        <?php if (isset($active_program->daily_calories) && $active_program->daily_calories) { ?>
                            <p><strong>Daily Calories:</strong> <?php echo $active_program->daily_calories; ?> kcal</p>
                        <?php } ?>
                        <?php if (isset($active_program->daily_protein) && $active_program->daily_protein) { ?>
                            <p><strong>Protein:</strong> <?php echo $active_program->daily_protein; ?>g</p>
                        <?php } ?>
                    </div>
                </div>
                <?php if (isset($active_program->objective) && $active_program->objective) { ?>
                    <hr>
                    <p><strong>Objective:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($active_program->objective)); ?></p>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No active program at the moment.
            </div>
        <?php } ?>

        <!-- Upcoming Consultations -->
        <?php if (!empty($upcoming_consultations)) { ?>
            <div class="program-box">
                <h3><i class="fa fa-calendar"></i> Upcoming Consultations</h3>
                <hr>
                <ul class="list-unstyled">
                    <?php foreach ($upcoming_consultations as $consultation) { ?>
                        <li style="margin-bottom: 10px;">
                            <i class="fa fa-calendar-o"></i>
                            <?php echo date('d/m/Y H:i', strtotime($consultation->consultation_date)); ?>
                            - <strong><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></strong>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
