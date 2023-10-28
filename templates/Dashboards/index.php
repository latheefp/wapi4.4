<?php
//debug($balance);
?>
<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group col-md-4">
                <label>Date and time range:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <input type="text" class="form-control float-right" name="dateRangeSelector" id="dateRangeSelector">
                </div>
            </div> 
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="templates"></h3>
                    <p>Templates</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="campaigns"></h3>
                    <p>Campaigns</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="schedules"></h3>
                    <p>Schedules</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="total_msg"></h3>
                    <p>Total Messages</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="receive"></h3>
                    <p>Receive Messages</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="send"></h3>
                    <p>Send Messages</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="rcvq" ></h3>
                    <p>Receive Queue</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="success_rate"></h3><sup style="font-size: 20px">%</sup>
                    <p>Success Rate</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="groups"></h3>
                    <p>Groups</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="contact_numbers"></h3>
                    <p>Saved Contacts</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="balance"></h3>
                    <p>Balance</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <!-- ./col -->
    </div>
    <!-- /.row -->

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-12 connectedSortable ui-sortable">

            <div class="row">



                <div class="col-md-6">



                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Message Status</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                <canvas id="MessageStatus" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 764px;" class="chartjs-render-monitor" width="764" height="250"></canvas>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">

                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Schedule Status</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="schedulebarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>

                    </div>
                </div>


            </div>

        </section>
    </div>
    <!-- /.row (main row) -->
</div><!-- /.container-fluid -->

<input id="start_date" type="hidden"><!-- comment -->
<input id="end_date" type="hidden"><!-- comment -->
<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
    $(function () {
        var ctxMessageStatusChart = document.getElementById("MessageStatus").getContext("2d");
        var MessageStatusChart = new Chart(ctxMessageStatusChart, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: "Success",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "rgba(75,192,192,1)",
                        borderWidth: 1, // Set the border width to 1 (thin)
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 0, // Set the point border width to 0 (no point)
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 0, // Set the point radius to 0 (no point)
                        pointHitRadius: 10,
                        data: [],
                        spanGaps: false,
                    },
                    {
                        label: "Failed",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: '#FF6384',
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(100,200,50,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: [],
                        spanGaps: false,
                    },
                    {
                        label: "Total",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "#36A2EB",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: [],
                        spanGaps: false,
                    }
                ]
            },
            options: {
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                }
            }
        });
        // function to update our chart
        var startDate = moment().subtract(1, 'days'); // For example, set it to one day ago
        var endDate = moment(); // Current date
        $('#start_date').val(startDate.format('YYYY-MM-DD hh:mm'));
        $('#end_date').val(endDate.format('YYYY-MM-DD hh:mm'));

        $('input[name="dateRangeSelector"]').daterangepicker({
            startDate: startDate,
            endDate: endDate,
            opens: 'left',
            timePicker: true,
            timePicker24Hour: true,
            pick12HourFormat: false,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }


        }, function (start, end, label) {
            $('#start_date').val(start.format('YYYY-MM-DD hh:mm'));
            $('#end_date').val(end.format('YYYY-MM-DD hh:mm'));
            var json_url = "/dashboards/getdata?start_date=" + start.format('YYYY-MM-DD hh:mm') + '&end_date=' + end.format('YYYY-MM-DD HH:mm');
            ajax_chart_sched(MessageStatusChart, json_url);
            ajax_chartbar(schedulebarChart, '/dashboards/getshedjson?start_date=' + start.format('YYYY-MM-DD hh:mm') + '&end_date=' + end.format('YYYY-MM-DD HH:mm'));
        });
        var ctxschedulebarChart = document.getElementById('schedulebarChart').getContext('2d');
        var schedulebarChart = new Chart(ctxschedulebarChart, {
            type: 'bar',
            data: {

                datasets: [
                    {
//                        label: 'Employee',
                        backgroundColor: '#caf270',
//                        data: [12, 59, 5, 56, 58, 12, 59, 87, 45],
                    }
                ]
            },
            options: {
                tooltips: {
                    displayColors: true,
                    callbacks: {
                        mode: 'x',
                    },
                },
                scales: {
                    xAxes: [
                        {
                            stacked: true,
                            gridLines: {
                                display: false,
                            },
                        },
                    ],
                    yAxes: [
                        {
                            stacked: true,
                            ticks: {
                                beginAtZero: true,
                            },
                            type: 'linear',
                        },
                    ],
                },
                responsive: true,
                maintainAspectRatio: false,
                legend: {position: 'bottom'},
            },
        })

//        $('input[name="dateRangeSelector"]').trigger('change');
        setInterval(fetchData, 60000); // Fetch data every 30 seconds


    });
    function ajax_chart_sched(chart, url, data) {
        var data = data || {};
        $.getJSON(url, data).done(function (response) {
            chart.data.labels = response.labels;
            chart.data.datasets[0].data = response.data.has_wa; // or you can iterate for multiple datasets
            chart.data.datasets[1].data = response.data.no_wa; // or you can iterate for multiple datasets
            chart.data.datasets[2].data = response.data.total; // or you can iterate for multiple datasets
            chart.update(); // finally update our chart
        });
    }


    function ajax_chartbar(chart, url, data) {
        var data = data || {};
        $.getJSON(url, data).done(function (response) {
            chart.data.labels = response.labels;
            chart.data.datasets[0].data = response.data.total; // or you can iterate for multiple datasets
            chart.update(); // finally update our chart
        });
    }

    function fetchData() {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();

        $.ajax({
            url: '/dashboards/fetchdata?start_date=' + start_date + '&end_date=' + end_date,
            method: 'GET', // Use 'POST' if needed
            dataType: 'json', // Adjust as per your response format
            success: function (jsonData) {
                for (var key in jsonData) {
                   // console.log(key);
                    //console.log(jsonData[key]);
                    if (jsonData.hasOwnProperty(key)) {
                        var textValue = jsonData[key];
                        var element = document.getElementById(key);
                        if (element) {
                            element.textContent = textValue;
                        } else {
                          //  console.log("Failed to set it");
                        }
                    }
                }
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    }




//</script>
<?php $this->Html->scriptEnd(); ?>