<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Système de Notation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
            max-width: 800px;
            margin: 0 auto;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .test-section h2 {
            margin-top: 0;
            color: #01807B;
        }
        .log {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
        }
        .log-entry {
            margin: 5px 0;
            padding: 5px;
            border-left: 3px solid #01807B;
        }
        .log-entry.error {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .log-entry.success {
            border-left-color: #28a745;
            background: #f0fff4;
        }
        .interactive-stars {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 20px 0;
        }
        .interactive-stars i {
            font-size: 36px;
            color: #dee2e6;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 44px;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .interactive-stars i:hover,
        .interactive-stars i.hover {
            color: #FFC107;
            transform: scale(1.15);
        }
        .interactive-stars i.selected {
            color: #FFC107;
        }
        .interactive-stars i.fa-star {
            color: #FFC107;
        }
        .interactive-stars i.fa-star-o {
            color: #dee2e6;
        }
        #submit-rating {
            width: 100%;
            padding: 12px;
            background: #01807B;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        #submit-rating:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: #ccc;
        }
        #submit-rating:not(:disabled):hover {
            background: #026660;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-badge.ok {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.error {
            background: #f8d7da;
            color: #721c24;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: Arial, sans-serif;
            resize: vertical;
        }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic Système de Notation</h1>

    <!-- Test 1: Vérification jQuery -->
    <div class="test-section">
        <h2>1. Vérification jQuery</h2>
        <div id="jquery-status"></div>
    </div>

    <!-- Test 2: Système d'étoiles -->
    <div class="test-section">
        <h2>2. Test des Étoiles Interactives</h2>
        <div class="info-box">
            <strong>Instructions:</strong> Cliquez sur les étoiles. Le bouton devrait s'activer automatiquement.
        </div>
        <div class="interactive-stars" id="stars-rating">
            <i class="fa fa-star-o" data-rating="1"></i>
            <i class="fa fa-star-o" data-rating="2"></i>
            <i class="fa fa-star-o" data-rating="3"></i>
            <i class="fa fa-star-o" data-rating="4"></i>
            <i class="fa fa-star-o" data-rating="5"></i>
        </div>
        <p>Note sélectionnée: <strong id="selected-rating">0</strong> étoile(s)</p>
        <p>État du bouton: <span id="button-state" class="status-badge error">Désactivé</span></p>
    </div>

    <!-- Test 3: Formulaire -->
    <div class="test-section">
        <h2>3. Test du Formulaire</h2>
        <form id="ratingForm">
            <textarea id="rating-comment" rows="3" placeholder="Votre commentaire (optionnel)"></textarea>
            <button type="submit" id="submit-rating" disabled>
                <i class="fa fa-check"></i> Enregistrer ma note
            </button>
        </form>
    </div>

    <!-- Test 4: Console de logs -->
    <div class="test-section">
        <h2>4. Console de Logs</h2>
        <div class="log" id="logs"></div>
    </div>

    <script>
        // Fonction de logging
        function addLog(message, type = 'info') {
            const logDiv = document.getElementById('logs');
            const entry = document.createElement('div');
            entry.className = 'log-entry ' + type;
            const timestamp = new Date().toLocaleTimeString();
            entry.innerHTML = `<strong>[${timestamp}]</strong> ${message}`;
            logDiv.appendChild(entry);
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        // Test 1: Vérifier jQuery
        const jqueryStatus = document.getElementById('jquery-status');
        if (typeof jQuery !== 'undefined') {
            jqueryStatus.innerHTML = '<span class="status-badge ok">✓ jQuery chargé (version ' + jQuery.fn.jquery + ')</span>';
            addLog('jQuery chargé avec succès', 'success');
        } else {
            jqueryStatus.innerHTML = '<span class="status-badge error">✗ jQuery non chargé</span>';
            addLog('ERREUR: jQuery non chargé', 'error');
        }

        // Test 2: Système d'étoiles
        $(document).ready(function() {
            addLog('Document ready', 'success');

            let selectedRating = 0;
            const starGroup = document.getElementById('stars-rating');
            const stars = starGroup.querySelectorAll('i');
            const submitBtn = document.getElementById('submit-rating');
            const ratingForm = document.getElementById('ratingForm');
            const selectedRatingDisplay = document.getElementById('selected-rating');
            const buttonStateDisplay = document.getElementById('button-state');

            addLog('Éléments trouvés: ' + stars.length + ' étoiles', 'success');

            function updateButtonState() {
                if (submitBtn.disabled) {
                    buttonStateDisplay.textContent = 'Désactivé';
                    buttonStateDisplay.className = 'status-badge error';
                } else {
                    buttonStateDisplay.textContent = 'Activé';
                    buttonStateDisplay.className = 'status-badge ok';
                }
            }

            // Star interactions
            stars.forEach(function(star, idx) {
                addLog('Attachement événements sur étoile ' + (idx + 1));

                // Hover effect
                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    highlightStars(rating);
                    addLog('Survol étoile ' + rating);
                });

                // Click/touch to select
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    addLog('Clic sur étoile ' + rating, 'success');
                    selectedRating = rating;
                    selectedRatingDisplay.textContent = rating;
                    markSelected(rating);
                    submitBtn.disabled = false;
                    updateButtonState();
                    addLog('Bouton activé!', 'success');

                    // Haptic feedback on mobile
                    if ('vibrate' in navigator) {
                        navigator.vibrate(10);
                        addLog('Vibration déclenchée');
                    }
                });
            });

            // Reset hover effect
            starGroup.addEventListener('mouseleave', function() {
                highlightStars(selectedRating);
                addLog('Retour à la sélection: ' + selectedRating);
            });

            function highlightStars(rating) {
                stars.forEach(function(star, index) {
                    if (index < rating) {
                        star.classList.remove('fa-star-o');
                        star.classList.add('fa-star');
                    } else {
                        star.classList.remove('fa-star');
                        star.classList.add('fa-star-o');
                    }
                });
            }

            function markSelected(rating) {
                stars.forEach(function(star, index) {
                    if (index < rating) {
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('selected');
                    }
                });
                highlightStars(rating);
            }

            // Form submission
            ratingForm.addEventListener('submit', function(e) {
                e.preventDefault();
                addLog('Formulaire soumis!', 'success');
                addLog('Note: ' + selectedRating + ' étoiles', 'info');
                addLog('Commentaire: ' + document.getElementById('rating-comment').value, 'info');

                if (selectedRating === 0) {
                    addLog('ERREUR: Aucune note sélectionnée', 'error');
                    alert('Veuillez sélectionner une note');
                    return;
                }

                // Simuler l'envoi
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Envoi en cours...';
                addLog('Simulation envoi AJAX...', 'info');

                setTimeout(function() {
                    submitBtn.innerHTML = '<i class="fa fa-check"></i> Enregistrer ma note';
                    submitBtn.disabled = selectedRating === 0;
                    addLog('Envoi simulé terminé', 'success');
                    alert('Test réussi! Le système de notation fonctionne correctement.');
                }, 2000);
            });

            addLog('Initialisation terminée', 'success');
            updateButtonState();
        });
    </script>
</body>
</html>
