<?php
//$va_statistics 	= $this->getVar('statistics_listing');
define("__ASSET_IMAGE_DIR__", __DIR__."/images");
define("__ASSET_IMAGE_URL__", __CA_URL_ROOT__."/app/plugins/suiviInventaireEglises/views/images");
?>

<h1>Suivi de l'inventaire des églises</h1>

<div class="suiviInventaire container">

    <div class="row">
        <div class="col-md-12">
            <div id="piechart" class="chart-container">
                <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2>Brabant-Wallon</h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="piscine" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="top_x_div2" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/bars.png" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2>Bruxelles</h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="bouboule" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="top_x_div2" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/bars.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2>Liège</h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="piscine" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="top_x_div2" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/bars.png" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2>Namur</h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="bouboule" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="top_x_div2" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/bars.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2>Tournai</h2>
            <div class="row">
                <div class="col-md-6">
                    <div id="piscine" class="chart-container">
                        <img src="<?php print __ASSET_IMAGE_URL__; ?>/pie.png"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="top_x_div2" class="chart-container">
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
    #piechart img {
        max-width: 40%;
    }
    .chart-container img {
        max-width: 100%;
        height: auto;
    }
</style>

<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
    // Load the Visualization API and the piechart package.
    google.charts.load('current', {'packages':['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var jsonData = $.ajax({
            url: "<?php print __CA_URL_ROOT__; ?>/index.php/suiviInventaireEglises/Statistics/Json",
            dataType: "json",
            async: false
        }).responseText;

        // Create our data table out of JSON data loaded from server.
        var data = new google.visualization.DataTable(jsonData);

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, {width: 400, height: 240});
    }
</script>