<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dietetic Program</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { padding-top: 20px; background: #f5f5f5; }
        .navbar-custom { background: #3498db; border: none; border-radius: 0; margin-bottom: 30px; }
        .navbar-custom .navbar-brand { color: white; }
        .navbar-custom .navbar-nav>li>a { color: white; }
        .no-access-box { background: white; padding: 60px 40px; text-align: center; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .no-access-box i { font-size: 80px; color: #3498db; margin-bottom: 20px; }
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
        <div class="no-access-box">
            <i class="fa fa-info-circle"></i>
            <h2>No Dietetic Program Found</h2>
            <p class="lead">You don't have access to a dietetic program yet.</p>
            <p>Please contact your dietitian to set up your personalized program.</p>
            <hr>
            <a href="<?php echo site_url('clients/profile'); ?>" class="btn btn-primary">
                <i class="fa fa-home"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
