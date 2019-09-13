$(document).ready(function () {
    /*
     * Initialise chart
     */
    var ctx = document.getElementById('myChart').getContext('2d');
    var mixedChart = new Chart(ctx, {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Afstand per activiteit in km',
                data: [],
                backgroundColor: 'rgba(80, 109, 153, 0.3)',
                borderWidth: 1,
                borderColor: 'rgba(80, 109, 153, 1)'
            }, {
                label: 'Totale afstand deze maand in km',
                data: [],
                backgroundColor: [
                    'rgba(255,0,0,0.0)'
                ],
                borderWidth: 5,
                borderColor: "#dc3545",
                // Changes this dataset to become a line
                type: 'line'
            }],
            labels: []
        },
        options: {
            title: {
                display: true,
                text: 'Afstand per activiteit ten opzichte van totale afstand deze maand',
                fontSize: 16,
                fontColor: 'black',
                fontFamily: 'Arial, Verdana'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        fontColor: "black"
                    },
                    gridLines: {
                        display: false,
                        color: "#000000",
                        fontColor: 'white',
                    },
                }],
                xAxes: [{
                    gridLines: {
                        display: false,
                        color: "#000000",
                        fontColor: 'black'
                    },
                    ticks: {
                        fontColor: "black"
                    }
                }]
            },
            legend: {
                labels: {
                    // This more specific font property overrides the global property
                    fontColor: 'black',
                    defaultFontFamily: 'Arial, Verdana'
                }
            }
        }
    });

    /*
     * Get distance in kilometres
     * @param distance in meters
     *
     * return int
     */
    function getDistance(distance) {
        return parseInt(((distance / 1000) * 100)) / 100;
    }

    /*
     * Get average hearthbeath over total activities
     * @param activities total activities
     * @param totalHearthBeath
     *
     * return int
     */
    function getAverageHearthBeath(activities, totalHearthBeath) {

        if (activities === 0) {
            return 0;
        } else {
            var result = totalHearthBeath / activities;
            return result.toFixed(1);
        }
    }

    /*
     * Get average elevation gain over total activities
     * @param activities total activities
     * @param totalElevationGain
     *
     * return int
     */
    function getAverageElevationGain(activities, totalElevationGain) {

        if (activities === 0) {
            return 0;
        } else {
            var result = totalElevationGain / activities;
            return result.toFixed(1);
        }
    }


    /*
     * Get average speed over total activities
     * @param activities total activities
     * @param totalAverageSpeed in seconds
     *
     * return int
     */
    function getAverageSpeedOverTotalActivities(activities, totalAverageSpeed) {
        if (activities === 0) {
            return 0;
        } else {
            var result = totalAverageSpeed / activities;
            return result;
        }
    }

    /*
     * Get moving time based on seconds
     * @param seconds
     * @return seconds in hours minutes and seconds
     */
    function getMovingTime(seconds) {
        var hours = String(Math.floor(seconds / 3600));
        if (hours.length == 1) {
            hours = '0' + hours;
        }
        var minutes = String(Math.floor((seconds / 60) % 60));
        if (minutes.length == 1) {
            minutes = '0' + minutes;
        }
        var seconds = String(seconds % 60);
        if (seconds.length == 1) {
            seconds = '0' + seconds;
        }
        return hours + ':' + minutes + ':' + seconds;
    }

    /*
     * Get average speed based on seconds
     * @param seconds
     * @return seconds in hours minutes and seconds
     */
    function getAverageSpeed(seconds) {

        if (seconds == 0) {
            return '00:00:00';
        }
        seconds = 1000 / seconds;
        var hours = String(Math.floor(seconds / 3600));
        if (hours.length == 1) {
            hours = '0' + hours;
        }
        var minutes = String(Math.floor((seconds / 60) % 60));
        if (minutes.length == 1) {
            minutes = '0' + minutes;
        }
        seconds = String(Math.floor(seconds % 60));
        if (seconds.length == 1) {
            seconds = '0' + seconds;
        }
        return hours + ':' + minutes + ':' + seconds;
    }


    /*
     * Get chart data based on year and/or month
     *
     * @return json
     */
    function getChartData() {
        var year = $('select#year').val();
        var month = $('select#month').val();
        $.ajax({
            type: 'POST',
            data: {
                year: year,
                month: month
            },
            url: "running-stats/getChartData",
            async: true,
            success: function (data) {
                if (data.success === true) {

                    var totalActivities = data.activities.activities.length;
                    var kmActivities = [];
                    var kmActivitiesCumulatief = [];
                    var activityLabels = [];
                    var totalDistance = 0;
                    var totalMovingTime = 0;
                    var totalAverageSpeed = 0;
                    var totalElevationGain = 0;
                    var totalAverageHearthBeath = 0
                    $('tbody#result').empty();

                    if (totalActivities > 0) {

                        $.each(data.activities.activities, function (index, activity) {
                            var distance = getDistance(activity.distance);
                            kmActivities.push(distance);
                            totalDistance = totalDistance + distance;
                            kmActivitiesCumulatief.push(totalDistance);
                            var startDate = moment(activity.startDate.date).format('DD-MM-YYYY');
                            activityLabels.push(startDate);
                            totalMovingTime = totalMovingTime + activity.movingTime;
                            totalAverageSpeed = totalAverageSpeed + activity.averageSpeed;
                            totalElevationGain = totalElevationGain + Math.round(activity.totalElevationGain);
                            totalAverageHearthBeath = totalAverageHearthBeath + activity.averageHeartrate;

                            var row = $('<tr class="clickableRow" data-activityid="' + activity.id + '">');

                            var workoutIcon = null;
                            if (activity.workoutType === 3) {
                                workoutIcon = '<i class="fas fa-dumbbell"></i>';
                            } else if (activity.workoutType === 1) {
                                workoutIcon = '<i class="fas fa-flag-checkered">';
                            }

                            row.append($('<td class="text-center">').html(workoutIcon));
                            row.append($('<td>').html(startDate));
                            row.append($('<td>').html(activity.name));
                            row.append($('<td>').html(distance));
                            row.append($('<td>').html(getAverageSpeed(activity.averageSpeed)));
                            row.append($('<td>').html(getMovingTime(activity.movingTime)));
                            row.append($('<td>').html(activity.averageHeartrate));
                            row.append($('<td>').html(Math.round(activity.totalElevationGain)));
                            $('tbody#result').append(row);
                        });
                    } else {
                        var row = $('<tr>');
                        $('tbody#result').append('<tr><td class="text-center" colspan="8">Nog geen activiteiten gelogd!</td></tr>');
                    }

                    //Load data into cards
                    $('h5#totalRunActivities').text(totalActivities);
                    $('h5#totalMovingTime').text(getMovingTime(totalMovingTime));
                    $('h5#totalDistance').text(totalDistance.toFixed(1) + ' km');
                    $('h5#totalAverageSpeed').text(getAverageSpeed(getAverageSpeedOverTotalActivities(totalActivities, totalAverageSpeed)));
                    $('h5#totalAverageElevationGain').text(getAverageElevationGain(totalActivities, totalElevationGain));
                    $('h5#totalAverageHearthBeat').text(getAverageHearthBeath(totalActivities, totalAverageHearthBeath));

                    //Load data into chart
                    mixedChart.data.labels = activityLabels;
                    mixedChart.data.datasets[0].data = kmActivities
                    mixedChart.data.datasets[1].data = kmActivitiesCumulatief;
                    mixedChart.update();
                } else {
                    alert(data.errorMessage);
                }
            }
        });
    }

    /*
     * When changing year or month select initialise getChartData
     */
    $('select#year, select#month').change(function () {
        getChartData();
    });


    $('body').on('click', 'tr.clickableRow', function () {
        var activityId = $(this).data('activityid');
        window.location.href = "/running-stats/detail/" + activityId;
    });


    getChartData();

});