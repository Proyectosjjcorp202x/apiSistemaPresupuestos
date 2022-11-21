<?php
require '../database.php';
?>
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>   
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {

            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                ['Work', 11],
                ['Eat', 2],
                ['Commute', 2],
                ['Watch TV', 2],
                ['Sleep', 7]
            ]);

            var options = {
                title: 'My Daily Activities'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            google.visualization.events.addListener(chart, 'ready', function () {
                var imageURI = chart.getImageURI();
                //console.log(imageURI);
                /*var s = new XMLSerializer().serializeToString($('#gauge_div').find('svg')[0]);
                 var image = new Image();
                 image.width = 640;
                 image.height = 480;
                 image.src = 'data:image/svg+xml;base64,' + window.btoa(s);
                 var myCanvas = document.createElement('canvas');
                 myCanvas.width = 640;
                 myCanvas.height = 480;
                 var myCanvasContext = myCanvas.getContext('2d');
                 myCanvasContext.drawImage(image, 0, 0);
                 // get google chart gague to base64, yey!
                 var base64String = myCanvas.toDataURL();
                 console.log(base64String);*/
                $.ajax({
                    type: 'POST', // Envío con método POST
                    url: './decodifica_base64.php', // Fichero destino (el PHP que trata los datos)
                    data: {base64String: imageURI} // Datos que se envían
                }).done(function (msg) {  // Función que se ejecuta si todo ha ido bien
                    $('#img').attr('src', 'grafica_generada.jpg');
                });
                //                $('#img').text(base64String);
                //  window.open('');
                //        $('#img').attr('src',base64String);
                //                console.log(gauge.getImageURI());

                //var imgUri = gauge.getChart().getImageURI();
                // do something with the image URI, like:
                //window.open(imgUri);
            });

            chart.draw(data, options);
        }

        google.charts.load('current', {'packages': ['gauge']});
        google.charts.setOnLoadCallback(drawChart2);

        function drawChart2() {
            var data = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['Memory', 80],
                ['CPU', 55],
                ['Network', 68]
            ]);

            var options = {
                width: 400, height: 120,
                redFrom: 90, redTo: 100,
                yellowFrom: 75, yellowTo: 90,
                minorTicks: 5
            };

            var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
            google.visualization.events.addListener(chart, 'ready', function () {
                let wait = setInterval(() => {
                    var s = new XMLSerializer().serializeToString($('#chart_div').find('svg')[1]);
                    var image = new Image();
                    image.width = 640;
                    image.height = 480;
                    image.src = 'data:image/svg+xml;base64,' + window.btoa(s);
                    var myCanvas = document.createElement('canvas');
                    myCanvas.width = 640;
                    myCanvas.height = 480;
                    var myCanvasContext = myCanvas.getContext('2d');
                    myCanvasContext.drawImage(image, 0, 0);
                    // get google chart gague to base64, yey!
                    var base64String = myCanvas.toDataURL();
                    console.log('src: ', base64String);
                    $.ajax({
                        type: 'POST', // Envío con método POST
                        url: './decodifica_base64.php', // Fichero destino (el PHP que trata los datos)
                        data: {base64String: base64String} // Datos que se envían
                    }).done(function (msg) {  // Función que se ejecuta si todo ha ido bien
                        $('#img').attr('src', 'grafica_generada2.jpg');
                        generaArchivo();
                    });
                }, 13000);
            });
            chart.draw(data, options);

            /*setInterval(function () {
             data.setValue(0, 1, 40 + Math.round(60 * Math.random()));
             chart.draw(data, options);
             }, 13000);
             setInterval(function () {
             data.setValue(0, 1, 40 + Math.round(60 * Math.random()));
             chart.draw(data, options);
             }, 13000);
             setInterval(function () {
             data.setValue(1, 1, 40 + Math.round(60 * Math.random()));
             chart.draw(data, options);
             }, 5000);
             setInterval(function () {
             data.setValue(2, 1, 60 + Math.round(20 * Math.random()));
             chart.draw(data, options);
             }, 26000);*/

        }


        function generaArchivo() {
            $.ajax({
                type: 'POST', // Envío con método POST
                url: './Graficos.php', // Fichero destino (el PHP que trata los datos)
                data: {contenido: $('#tabla').html()} // Datos que se envían
            }).done(function (msg) {  // Función que se ejecuta si todo ha ido bien
                //console.log(msg);
                $.ajax({
                    type: 'POST', // Envío con método POST
                    url: './GuardarArchivo.php', // Fichero destino (el PHP que trata los datos)
                    data: {contenido: msg} // Datos que se envían
                }).done(function (msg) {  // Función que se ejecuta si todo ha ido bien
                    console.log(msg);  // Escribimos en el div consola el mensaje devuelto
                });
            });
        }

    </script>
</head>
<div id="tabla">
    <table>
        <tbody>
            <tr>
                <th colspan="10" style="background-color: red;color:white">
                    <b>Listado en tabla excel</b>
                </th>
            </tr>
            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
                <td>6</td>
                <td>7</td>
                <td>8</td>
                <td>9</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10">
                    <img id="img" />
                </td>
            </tr>
        </tbody>
    </table>    
</div>
<div id="piechart" style="width:280px; height: 140px;"></div>
<div id="chart_div" style="width:280px; height: 140px;"></div>