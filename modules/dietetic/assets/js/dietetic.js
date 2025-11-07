/**
 * Dietetic Module JavaScript
 */

(function($) {
    'use strict';

    /**
     * Initialize datatables
     * NOTE: Commented out auto-initialization to allow each page to configure its own DataTable options
     * Each list page now initializes its own DataTable with specific settings (search, pagination, etc.)
     */
    function initDatatables() {
        // Auto-initialization disabled - each page manages its own DataTable
        // if ($('.dietetic-table').length) {
        //     $('.dietetic-table').DataTable({
        //         responsive: true,
        //         language: {
        //             url: app.lang.datatables
        //         }
        //     });
        // }
    }

    /**
     * Initialize Select2
     */
    function initSelect2() {
        if ($('.dietetic-select2').length) {
            $('.dietetic-select2').select2({
                width: '100%'
            });
        }

        // Patient search
        if ($('#patient_search').length) {
            $('#patient_search').select2({
                ajax: {
                    url: admin_url + 'dietetic/patients/search',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 2
            });
        }

        // Food search
        if ($('#food_search').length) {
            $('#food_search').select2({
                ajax: {
                    url: admin_url + 'dietetic/foods/search',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 2
            });
        }
    }

    /**
     * Initialize date pickers
     */
    function initDatePickers() {
        if ($('.dietetic-datepicker').length) {
            $('.dietetic-datepicker').datepicker({
                format: app.options.date_format,
                autoclose: true,
                todayHighlight: true
            });
        }

        if ($('.dietetic-datetimepicker').length) {
            $('.dietetic-datetimepicker').datetimepicker({
                format: app.options.date_format + ' ' + app.options.time_format,
                autoclose: true,
                todayHighlight: true
            });
        }
    }

    /**
     * BMI Calculator
     */
    function calculateBMI() {
        var weight = parseFloat($('#weight').val());
        var height = parseFloat($('#height').val());

        if (weight && height && height > 0) {
            var heightM = height / 100;
            var bmi = weight / (heightM * heightM);
            $('#bmi').val(bmi.toFixed(2));

            // Update BMI category
            var category = '';
            if (bmi < 18.5) {
                category = 'Underweight';
            } else if (bmi < 25) {
                category = 'Normal';
            } else if (bmi < 30) {
                category = 'Overweight';
            } else {
                category = 'Obese';
            }

            $('#bmi_category').text(category);
        }
    }

    /**
     * Add measurement modal
     */
    function addMeasurement() {
        var form = $('#add_measurement_form');
        var formData = form.serialize();

        $.ajax({
            url: admin_url + 'dietetic/patients/add_measurement',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    $('#addMeasurementModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    alert_float('danger', response.message || 'An error occurred');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert_float('danger', 'Failed to save measurement. Please check console for details.');
            }
        });
    }

    /**
     * Delete confirmation
     */
    function deleteConfirm(url, callback) {
        if (confirm('Are you sure you want to delete this item?')) {
            $.post(url, function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    if (callback) callback();
                } else {
                    alert_float('danger', response.message);
                }
            }, 'json');
        }
    }

    /**
     * Add food to meal
     */
    function addFoodToMeal(mealId) {
        var foodId = $('#food_select_' + mealId).val();
        var quantity = $('#food_quantity_' + mealId).val();
        var unit = $('#food_unit_' + mealId).val();

        if (!foodId || !quantity) {
            alert_float('warning', 'Please select a food and enter quantity');
            return;
        }

        $.post(admin_url + 'dietetic/programs/add_food_to_meal', {
            meal_id: mealId,
            food_id: foodId,
            quantity: quantity,
            unit: unit
        }, function(response) {
            if (response.success) {
                alert_float('success', response.message);
                location.reload();
            } else {
                alert_float('danger', response.message);
            }
        }, 'json');
    }

    /**
     * Remove food from meal
     */
    function removeFoodFromMeal(foodMealId) {
        if (confirm('Remove this food from the meal?')) {
            $.post(admin_url + 'dietetic/programs/remove_food_from_meal/' + foodMealId, function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            }, 'json');
        }
    }

    /**
     * Load weight evolution chart
     */
    function loadWeightChart(patientId, canvasId) {
        $.get(admin_url + 'dietetic/patients/chart_data/' + patientId, function(response) {
            if (response.success) {
                var ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.data.labels,
                        datasets: [{
                            label: 'Weight (kg)',
                            data: response.data.weights,
                            borderColor: 'rgb(52, 152, 219)',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            tension: 0.4
                        }, {
                            label: 'BMI',
                            data: response.data.bmis,
                            borderColor: 'rgb(231, 76, 60)',
                            backgroundColor: 'rgba(231, 76, 60, 0.1)',
                            tension: 0.4,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Weight (kg)'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'BMI'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            }
        }, 'json');
    }

    /**
     * Initialize consultation calendar
     */
    function initConsultationCalendar() {
        if ($('#consultation_calendar').length) {
            var calendarEl = document.getElementById('consultation_calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {
                    url: admin_url + 'dietetic/consultations/get_calendar_data',
                    failure: function() {
                        alert_float('danger', 'Failed to load events');
                    }
                },
                eventClick: function(info) {
                    window.location.href = info.event.url;
                }
            });
            calendar.render();
        }
    }

    /**
     * Calculate meal nutrition totals
     */
    function calculateMealNutrition(mealElement) {
        var totalCalories = 0;
        var totalProtein = 0;
        var totalCarbs = 0;
        var totalFats = 0;

        $(mealElement).find('.food-item').each(function() {
            var calories = parseFloat($(this).data('calories')) || 0;
            var protein = parseFloat($(this).data('protein')) || 0;
            var carbs = parseFloat($(this).data('carbs')) || 0;
            var fats = parseFloat($(this).data('fats')) || 0;
            var quantity = parseFloat($(this).find('.food-quantity-input').val()) || 0;
            var servingSize = parseFloat($(this).data('serving-size')) || 100;

            var ratio = quantity / servingSize;

            totalCalories += calories * ratio;
            totalProtein += protein * ratio;
            totalCarbs += carbs * ratio;
            totalFats += fats * ratio;
        });

        $(mealElement).find('.meal-calories-total').text(Math.round(totalCalories));
        $(mealElement).find('.meal-protein-total').text(totalProtein.toFixed(1));
        $(mealElement).find('.meal-carbs-total').text(totalCarbs.toFixed(1));
        $(mealElement).find('.meal-fats-total').text(totalFats.toFixed(1));
    }

    /**
     * Export functionality
     */
    function exportData(type) {
        window.location.href = admin_url + 'dietetic/export/' + type;
    }

    /**
     * Force expand Diététique menu in admin sidebar
     */
    function expandDieteticMenu() {
        // Check if we're on a dietetic module page
        if (window.location.href.indexOf('/admin/dietetic') !== -1) {
            // Try multiple times to ensure menu is loaded
            var attempts = 0;
            var maxAttempts = 10;

            var tryExpand = function() {
                attempts++;

                // Try different possible selectors
                var $dieteticMenuItem = $('li.menu-item-dietetic');

                // If not found, try finding by link href
                if (!$dieteticMenuItem.length) {
                    $('aside#aside_menu a[href*="/admin/dietetic"]').each(function() {
                        var $link = $(this);
                        var $parent = $link.closest('li.menu-item');
                        if ($parent.length && $link.attr('href') === admin_url + 'dietetic') {
                            $dieteticMenuItem = $parent;
                            return false; // break
                        }
                    });
                }

                if ($dieteticMenuItem.length) {
                    console.log('Dietetic menu found, expanding...');

                    // Add active class to parent
                    $dieteticMenuItem.addClass('active');

                    // Find the parent link and set aria-expanded
                    var $menuLink = $dieteticMenuItem.find('> a');
                    if ($menuLink.length) {
                        $menuLink.attr('aria-expanded', 'true');
                        console.log('Set aria-expanded on parent link');
                    }

                    // Find submenu and expand it
                    var $submenu = $dieteticMenuItem.find('> ul');
                    if ($submenu.length) {
                        $submenu.addClass('in').css('display', 'block');
                        console.log('Submenu expanded');
                    }

                    // Also mark the active child menu item
                    var currentUrl = window.location.href;
                    $dieteticMenuItem.find('li').each(function() {
                        var $childLink = $(this).find('a');
                        if ($childLink.length && currentUrl.indexOf($childLink.attr('href')) !== -1) {
                            $(this).addClass('active');
                        }
                    });
                } else if (attempts < maxAttempts) {
                    // Retry after a short delay
                    console.log('Dietetic menu not found yet, retrying... (attempt ' + attempts + ')');
                    setTimeout(tryExpand, 100);
                } else {
                    console.log('Dietetic menu not found after ' + maxAttempts + ' attempts');
                }
            };

            // Start trying to expand
            tryExpand();
        }
    }

    // Document ready
    $(document).ready(function() {
        initDatatables();
        initSelect2();
        initDatePickers();
        initConsultationCalendar();

        // Force expand Diététique menu on dietetic pages
        expandDieteticMenu();

        // Auto-calculate BMI
        $('#weight, #height').on('input', calculateBMI);

        // Global event handlers
        window.dietetic = {
            addMeasurement: addMeasurement,
            deleteConfirm: deleteConfirm,
            addFoodToMeal: addFoodToMeal,
            removeFoodFromMeal: removeFoodFromMeal,
            loadWeightChart: loadWeightChart,
            calculateMealNutrition: calculateMealNutrition,
            exportData: exportData
        };
    });

})(jQuery);
