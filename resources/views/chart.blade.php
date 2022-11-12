<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Sesiones de Google Analytics</title>
</head>
<body>
    <h1>Sesiones de Google Analytics</h1>
    <table>
        <tr>
            <td width="88%">
                <div id="container"></div>
            </td>
            <td width="12%">
                <input type="text" name="daterange" value="09/01/2022 - 10/31/2022" />
            </td>
        </tr>
    </table>
</body>
<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">

    function printchart(data) {
        Highcharts.chart('container', {
            title: {
                text: 'Sesiones por fecha'
            },
            xAxis: {
                categories: data.categorias
            },
            yAxis: {
                title: {
                    text: 'Sesiones'
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            plotOptions: {
                series: {
                    allowPointSelect: true
                }
            },
            series: [{
                name: 'Sesiones',
                data: data.sesiones
            }],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        }); 
    }

    $(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
    }, function(start, end, label) {
        // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        $.get("/show?inicio=" + start.format('YYYY-MM-DD') + "&fin=" + end.format('YYYY-MM-DD'), function(data){ printchart(data) });
    });
    });

    $.get("/show", function(data){ printchart(data) });

</script>
</html>