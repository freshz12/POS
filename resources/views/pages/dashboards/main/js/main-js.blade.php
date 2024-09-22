<script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
<script src="{{ asset('library/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('library/chart.js/dist/Chart.js') }}"></script>
<script src="{{ asset('library/owl.carousel/dist/owl.carousel.min.js') }}"></script>
<script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#start-range-selector').val(null);
        $('#end-range-selector').val(null);
        $('#range-selector').val('today');

        $.ajax({
            url: '/dashboards/main/index_data',
            method: 'GET',
            success: function(response) {
                var chartsData = [{
                        ctx: document.getElementById("chart1").getContext('2d'),
                        data: response.pieChartData1,
                        labels: response.pieChartLabels1,
                        backgroundColor: ['#B22222', '#FF8C00', '#FFD700', '#228B22',
                            '#1E90FF'],
                        additionals: response.pieChartAdditionals1
                    },
                    {
                        ctx: document.getElementById("chart2").getContext('2d'),
                        data: response.pieChartData2,
                        labels: response.pieChartLabels2,
                        backgroundColor: ['#B22222', '#FF8C00', '#FFD700', '#228B22',
                            '#1E90FF'],
                        additionals: response.pieChartAdditionals2
                    },
                    {
                        ctx: document.getElementById("chart3").getContext('2d'),
                        data: response.pieChartData3,
                        labels: response.pieChartLabels3,
                        backgroundColor: ['#B22222', '#FF8C00', '#FFD700', '#228B22',
                            '#1E90FF'],
                        additionals: response.pieChartAdditionals3
                    }
                ];

                chartsData.forEach((chart, index) => {
                    new Chart(chart.ctx, {
                        type: 'pie',
                        data: {
                            datasets: [{
                                data: chart.data,
                                backgroundColor: chart.backgroundColor,
                                label: 'Dataset'
                            }],
                            labels: chart.labels,
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            legend: {
                                display: false,
                            },
                            tooltips: {
                                callbacks: {
                                    label: function(tooltipItem, data) {
                                        var label = data.labels[tooltipItem
                                            .index];
                                        var totalAmount = chart.data[tooltipItem
                                            .index];
                                        var additionalData = chart.additionals ?
                                            chart.additionals[tooltipItem
                                            .index] : 'N/A';

                                        if (index === 1) {
                                            return [
                                                'Capster Name: ' + label,
                                                'Total Amount: ' +
                                                formatNumberWithCommas(
                                                    totalAmount),
                                                'Total Transactions: ' +
                                                additionalData
                                            ];
                                        } else if (index === 2) {
                                            return [
                                                'Product Name: ' + label,
                                                'Units Sold: ' +
                                                totalAmount,
                                                'Unit Price: ' +
                                                formatNumberWithCommas(
                                                    additionalData)
                                            ];
                                        } else {
                                            return [
                                                'Customer Name: ' + label,
                                                'Total Spent: ' +
                                                formatNumberWithCommas(
                                                    totalAmount),
                                                'Total Transactions: ' +
                                                additionalData
                                            ];
                                        }
                                    }
                                },
                                backgroundColor: '#ffffff',
                                titleFontColor: '#000',
                                bodyFontColor: '#000',
                                titleFontSize: 16,
                                bodyFontSize: 14,
                                displayColors: false,
                                borderColor: '#000',
                                borderWidth: 1
                            }
                        }
                    });
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });

        $('#mainChart').empty();
        var defaultRange = $('#range-selector').val();
        fetchLineChartData(defaultRange);
        fetchTotalAmountAndTransactions(defaultRange);

        $('#range-selector').on('change', function() {
            $('#mainChart').empty();
            var selectedRange = $(this).val();

            // Get start and end date values from the inputs
            var startRange = $('#start-date').val();
            var endRange = $('#end-date').val();

            if (selectedRange === 'custom') {
                $('#customRangeModal').modal('show');
            } else {
                fetchLineChartData(selectedRange);
                fetchTotalAmountAndTransactions(selectedRange);
            }
        });

        $('#apply-custom-range').on('click', function() {
            var startRange = $('#start-date').val();
            var endRange = $('#end-date').val();

            if (startRange && endRange) {
                $('#mainChart').empty();
                fetchLineChartData('custom', startRange, endRange);
                fetchTotalAmountAndTransactions('custom', startRange, endRange);
                $('#customRangeModal').modal('hide');
            } else {
                alert("Please select both start and end dates.");
            }
        });
    });

    let myLineChart; // Store the chart instance

    function fetchLineChartData(range, startDate = null, endDate = null) {
        $.ajax({
            url: '/dashboards/mainLine',
            method: 'GET',
            data: {
                range: range,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                var lineChartData = response.lineChartData;
                var lineChartLabels = response.lineChartLabels;

                // Destroy the existing chart if it exists
                if (myLineChart) {
                    myLineChart.destroy();
                }

                var ctxLine = document.getElementById("mainChart").getContext('2d');
                myLineChart = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: lineChartLabels,
                        datasets: [{
                            label: 'Amount',
                            data: lineChartData,
                            borderWidth: 2,
                            backgroundColor: '#6777ef',
                            borderColor: '#6777ef',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#ffffff',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                gridLines: {
                                    drawBorder: false,
                                    color: '#f2f2f2',
                                },
                                ticks: {
                                    beginAtZero: true,
                                    callback: function(value) {
                                        return formatNumberWithCommas(value);
                                    }
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    display: true
                                },
                                gridLines: {
                                    display: false
                                }
                            }]
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching line chart data:', error);
            }
        });
    }

    function fetchTotalAmountAndTransactions(range, startDate = null, endDate = null) {
        $.ajax({
            url: '/dashboards/mainTotal',
            method: 'GET',
            data: {
                range: range,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                $('#total-orders').text(response.total_transactions);
                $('#balance').text(formatNumberWithCommas(response.total_amount));
            },
            error: function(xhr, status, error) {
                console.error('Error fetching total data:', error);
            }
        });
    }

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>
