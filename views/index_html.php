<?php
//$va_statistics 	= $this->getVar('statistics_listing');
define("__ASSET_IMAGE_DIR__", __DIR__."/images");
define("__ASSET_IMAGE_URL__", __CA_URL_ROOT__."/app/plugins/suiviInventaireEglises/views/images");
$vs_statistiques_globales = $this->getVar("statistiques_globales");
//var_dump($vs_statistiques_globales);die();
?>

<h1>Suivi de l'inventaire des églises</h1>

<div class="suiviInventaire container">

    <div class="row">
        <div class="col-md-12">
            <h2>CIPAR</h2>
            <div class="chart-container">
                <div id="cipar">
                    <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
                </div>
            </div>
            <table class="cipar_table">
            <?php
            foreach($vs_statistiques_globales as $row):
                print "<tr>";
                foreach($row as $col):
                    print "<td>$col</td>\n";
                endforeach;
                print "</tr>";
            endforeach;
            ?>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2><a href="/gestion/index.php/suiviInventaireEglises/Statistics/Eglises/diocese/brabant_wallont">Brabant-Wallon</a></h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="brabant_wallon" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="brabant_wallon_bars" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/bars.png" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2><a href="/gestion/index.php/suiviInventaireEglises/Statistics/Eglises/diocese/bruxelles">Bruxelles</a></h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="bruxelles" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="bruxelles_bars" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/bars.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2><a href="/gestion/index.php/suiviInventaireEglises/Statistics/Eglises/diocese/liege">Liège</a></h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="liege" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="liege_bars" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/bars.png" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2><a href="/gestion/index.php/suiviInventaireEglises/Statistics/Eglises/diocese/namur">Namur</a></h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="namur" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="namur_bars" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/bars.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2><a href="/gestion/index.php/suiviInventaireEglises/Statistics/Eglises/diocese/tournai">Tournai</a></h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="tournai" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="tournai_bars" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/bars.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div style="margin-bottom:100px;clear:both;"></div>

<style>
    .suiviInventaire > div,
    .suiviInventaire > div > div {
        /*border:1px solid red;*/
    }
    .suiviInventaire h2 {
        font-size:18px;
    }
    .suiviInventaire .row {
        width:100%;
        clear:both;
    }
    .col-md-6 {
        width:46%;
        margin-right:3%;
        margin-left:0;
        float:left;
        /*border:1px solid blue;*/
        min-height: 40px;
    }
    .col-md-6:last-child {
        margin-right:0;
    }
    .col-md-6:first-child {
        margin-left:1.5%;
    }
    .chart-container {
        text-align: center;
    }
    .suiviInventaire img {
        max-width: 80%;
        height: auto;
    }
    #cipar img {
        width: auto;
        max-height: 240px;
    }
    table.cipar_table {
        margin:auto;
        margin-bottom:40px;

    }
    table tr:nth-child(2n+1) {
        background-color:lightgrey;
    }
    table td {
        padding:10px 20px;
    }
</style>

<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
    // Load the Visualization API and the piechart package.
    google.charts.load('current', {'packages':['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        var jsonData1 = $.ajax({
            url: "<?php print __CA_URL_ROOT__; ?>/index.php/suiviInventaireEglises/Statistics/Json",
            dataType: "json",
            async: false
        }).responseText;
        var jsonData2 = $.ajax({
            url: "<?php print __CA_URL_ROOT__; ?>/index.php/suiviInventaireEglises/Statistics/Json/diocese/brabant_wallon",
            dataType: "json",
            async: false
        }).responseText;
        var jsonData3 = $.ajax({
            url: "<?php print __CA_URL_ROOT__; ?>/index.php/suiviInventaireEglises/Statistics/Json/diocese/bruxelles",
            dataType: "json",
            async: false
        }).responseText;
        var jsonData4 = $.ajax({
            url: "<?php print __CA_URL_ROOT__; ?>/index.php/suiviInventaireEglises/Statistics/Json/diocese/liege",
            dataType: "json",
            async: false
        }).responseText;
        var jsonData5 = $.ajax({
            url: "<?php print __CA_URL_ROOT__; ?>/index.php/suiviInventaireEglises/Statistics/Json/diocese/namur",
            dataType: "json",
            async: false
        }).responseText;
        var jsonData6 = $.ajax({
            url: "<?php print __CA_URL_ROOT__; ?>/index.php/suiviInventaireEglises/Statistics/Json/diocese/tournai",
            dataType: "json",
            async: false
        }).responseText;


        var pie_options_large = {
            legend: 'none',
            pieSliceText: 'label',
            width: "100%", height: 240
        };
        var pie_options = {
            legend: 'none',
            pieSliceText: 'label',
            width: "100%", height: 120
        };
        var histogram_options = {
            legend: { position: 'none' },
            height: 120
        };

        // Create our data table out of JSON data loaded from server.
        var data1 = new google.visualization.DataTable(jsonData1);
        var data2 = new google.visualization.DataTable(jsonData2);
        var data3 = new google.visualization.DataTable(jsonData3);
        var data4 = new google.visualization.DataTable(jsonData4);
        var data5 = new google.visualization.DataTable(jsonData5);
        var data6 = new google.visualization.DataTable(jsonData6);

        // Instantiate and draw our chart, passing in some options.
        var chart1 = new google.visualization.PieChart(document.getElementById('cipar'));
        chart1.draw(data1, pie_options_large);

        var chart2 = new google.visualization.PieChart(document.getElementById('brabant_wallon'));
        chart2.draw(data2, pie_options);
        var histogram1 = new google.visualization.ColumnChart(document.getElementById('brabant_wallon_bars'));
        histogram1.draw(data2, histogram_options);

        var chart3 = new google.visualization.PieChart(document.getElementById('bruxelles'));
        chart3.draw(data3, pie_options);
        var histogram2 = new google.visualization.ColumnChart(document.getElementById('bruxelles_bars'));
        histogram2.draw(data3, histogram_options);

        var chart4 = new google.visualization.PieChart(document.getElementById('liege'));
        chart4.draw(data4, pie_options);
        var histogram3 = new google.visualization.ColumnChart(document.getElementById('liege_bars'));
        histogram3.draw(data4, histogram_options);

        var chart5 = new google.visualization.PieChart(document.getElementById('namur'));
        chart5.draw(data5, pie_options);
        var histogram4 = new google.visualization.ColumnChart(document.getElementById('namur_bars'));
        histogram4.draw(data5, histogram_options);

        var chart6 = new google.visualization.PieChart(document.getElementById('tournai'));
        chart6.draw(data6, pie_options);
        var histogram5 = new google.visualization.ColumnChart(document.getElementById('tournai_bars'));
        histogram5.draw(data6, histogram_options);
    }
</script>