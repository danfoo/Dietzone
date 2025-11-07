/**
 * Dietetic Module JavaScript
 */

// Check if jQuery is available
if (typeof jQuery === 'undefined') {
    console.error('DIETETIC.JS: jQuery is not loaded!');
} else {
    console.log('DIETETIC.JS: jQuery is loaded, version: ' + jQuery.fn.jquery);
}

(function($) {
    'use strict';

    // Verify we're inside the closure properly
    console.log('DIETETIC.JS: Initializing module...');

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
            // Wait for window to fully load (after Perfex's own menu initialization)
            setTimeout(function() {
                console.log('Attempting to activate Dietetic menu...');

                // Find the parent Dietetic menu item
                var $dieteticMenuItem = null;

                // Look for the menu item by finding the main Dietetic link
                $('aside.sidebar-menu a[href*="/admin/dietetic"]').each(function() {
                    var $link = $(this);
                    var href = $link.attr('href');

                    // Find the parent menu item (not child)
                    if (href && (href === admin_url + 'dietetic' || href === admin_url + 'dietetic/')) {
                        $dieteticMenuItem = $link.closest('li');
                        console.log('Found Dietetic parent menu item');
                        return false; // break
                    }
                });

                if ($dieteticMenuItem && $dieteticMenuItem.length) {
                    // Get current URL path
                    var currentUrl = window.location.href;
                    var currentPath = window.location.pathname;
                    var foundActiveChild = false;

                    console.log('Current URL: ' + currentUrl);
                    console.log('Current Path: ' + currentPath);

                    // Find all child menu items
                    $dieteticMenuItem.find('ul li').each(function() {
                        var $child = $(this);
                        var $childLink = $child.find('a');

                        if ($childLink.length) {
                            var childHref = $childLink.attr('href');

                            if (childHref) {
                                // Extract path from href (handle both full URLs and paths)
                                var childPath = childHref;
                                if (childHref.indexOf('http') === 0) {
                                    // It's a full URL, extract the path
                                    try {
                                        var url = new URL(childHref);
                                        childPath = url.pathname;
                                    } catch (e) {
                                        console.log('Error parsing URL: ' + childHref);
                                    }
                                }

                                console.log('Checking child: ' + childPath);

                                // Check if current path matches this child
                                // Use exact match or startsWith for child routes
                                if (currentPath === childPath ||
                                    (childPath !== admin_url + 'dietetic' &&
                                     childPath !== admin_url + 'dietetic/' &&
                                     currentPath.indexOf(childPath) === 0)) {

                                    // Mark this child as active
                                    $child.addClass('active');
                                    foundActiveChild = true;
                                    console.log('✓ Marked child as active: ' + childPath);
                                }
                            }
                        }
                    });

                    // Always expand parent on dietetic pages (not just when child is active)
                    console.log('Expanding parent menu (foundActiveChild: ' + foundActiveChild + ')');

                    // Mark parent as active
                    $dieteticMenuItem.addClass('active');

                    // Expand the submenu
                    var $submenu = $dieteticMenuItem.find('> ul');
                    if ($submenu.length) {
                        $submenu.addClass('in').show();
                        console.log('Expanded parent menu');
                    }

                    // Set aria-expanded on parent link
                    var $parentLink = $dieteticMenuItem.find('> a');
                    if ($parentLink.length) {
                        $parentLink.attr('aria-expanded', 'true');
                        console.log('Set aria-expanded on parent link');
                    }
                } else {
                    console.log('Could not find Dietetic parent menu item');
                }
            }, 500); // Wait 500ms to ensure Perfex has initialized its menu
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

})(window.jQuery || window.$);
