/**
 * Dietetic Module Portal JavaScript
 */

(function($) {
    'use strict';

    /**
     * Add measurement from portal
     */
    function addMeasurement() {
        var form = $('#portal_add_measurement_form');
        var formData = form.serialize();

        $.post(site_url + 'dietetic/portal/add_measurement', formData, function(response) {
            if (response.success) {
                alert('Measurement added successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }

    /**
     * Load portal weight chart
     */
    function loadPortalWeightChart(canvasId, weightData, bmiData, labels) {
        var ctx = document.getElementById(canvasId).getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Weight (kg)',
                    data: weightData,
                    borderColor: 'rgb(52, 152, 219)',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'BMI',
                    data: bmiData,
                    borderColor: 'rgb(46, 204, 113)',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Weight (kg)'
                        },
                        beginAtZero: false
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
                        },
                        beginAtZero: false
                    }
                }
            }
        });
    }

    /**
     * Initialize date pickers for portal
     */
    function initPortalDatePickers() {
        if ($('.portal-datepicker').length) {
            $('.portal-datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
        }
    }

    /**
     * Toggle meal plan details
     */
    function toggleMealPlanDetails(planId) {
        $('#meal_plan_details_' + planId).slideToggle();
    }

    /**
     * Print meal plan
     */
    function printMealPlan() {
        window.print();
    }

    /**
     * Download meal plan PDF
     */
    function downloadMealPlanPDF(planId) {
        window.location.href = site_url + 'dietetic/portal/download_meal_plan/' + planId;
    }

    // Document ready
    $(document).ready(function() {
        initPortalDatePickers();

        // Global portal functions
        window.dietetic_portal = {
            addMeasurement: addMeasurement,
            loadWeightChart: loadPortalWeightChart,
            toggleMealPlanDetails: toggleMealPlanDetails,
            printMealPlan: printMealPlan,
            downloadMealPlanPDF: downloadMealPlanPDF
        };
    });

})(jQuery);
