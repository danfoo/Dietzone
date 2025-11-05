<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Measurement</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { padding-top: 20px; background: #f5f5f5; }
        .navbar-custom { background: #3498db; border: none; border-radius: 0; margin-bottom: 30px; }
        .navbar-custom .navbar-brand { color: white; }
        .navbar-custom .navbar-nav>li>a { color: white; }
        .form-container { background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .mobile-menu-toggle { display: none; }

        @media (max-width: 768px) {
            .navbar-header { float: none; }
            .navbar-toggle { display: block; }
            .navbar-collapse { border-top: 1px solid transparent; box-shadow: inset 0 1px 0 rgba(255,255,255,0.1); }
            .navbar-collapse.collapse { display: none!important; }
            .navbar-nav { float: none!important; margin: 7.5px -15px; }
            .navbar-nav>li { float: none; }
            .navbar-nav>li>a { padding-top: 10px; padding-bottom: 10px; }
            .navbar-collapse.collapse.in { display: block!important; }
            .navbar-fixed-top .navbar-collapse { max-height: 340px; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo site_url('dietetic/portal'); ?>">
                    <i class="fa fa-heartbeat"></i> Programme Diététique
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo site_url('dietetic/portal'); ?>"><i class="fa fa-home"></i> Tableau de bord</a></li>
                    <li><a href="<?php echo site_url('clients/profile'); ?>"><i class="fa fa-user"></i> Profil</a></li>
                    <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="form-container">
                    <h2><i class="fa fa-plus-circle text-success"></i> Ajouter une Mesure</h2>
                    <hr>

                    <?php if (isset($error)) { ?>
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php } ?>

                    <?php if (isset($success)) { ?>
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i> <?php echo $success; ?>
                        </div>
                    <?php } ?>

                    <form method="POST" action="<?php echo site_url('dietetic/portal/add_measurement'); ?>">
                        <div class="form-group">
                            <label>Date *</label>
                            <input type="date" name="measurement_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Poids (kg) *</label>
                                    <input type="number" name="weight" class="form-control" step="0.1" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Masse Grasse (%)</label>
                                    <input type="number" name="body_fat" class="form-control" step="0.1" min="0" max="100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Masse Musculaire (%)</label>
                                    <input type="number" name="muscle_mass" class="form-control" step="0.1" min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tour de Taille (cm)</label>
                                    <input type="number" name="waist" class="form-control" step="0.1" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tour de Hanches (cm)</label>
                                    <input type="number" name="hips" class="form-control" step="0.1" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tour de Poitrine (cm)</label>
                                    <input type="number" name="chest" class="form-control" step="0.1" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tour de Bras (cm)</label>
                                    <input type="number" name="arms" class="form-control" step="0.1" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tour de Cuisses (cm)</label>
                                    <input type="number" name="thighs" class="form-control" step="0.1" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Remarques, observations..."></textarea>
                        </div>

                        <hr>
                        <div class="form-group">
                            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Enregistrer la Mesure
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
