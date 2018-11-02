<?php
//$va_statistics 	= $this->getVar('statistics_listing');
define("__ASSET_IMAGE_DIR__", __DIR__."/images");
define("__ASSET_IMAGE_URL__", __CA_URL_ROOT__."/app/plugins/suiviInventaireEglises/views/images");
$vs_statistiques_globales = $this->getVar("statistiques_globales");
$vs_diocese = $this->getVar("diocese");
$va_eglises = $this->getVar("eglises");
//var_dump($vs_statistiques_globales);die();
?>

<h1><?php print $vs_diocese; ?></h1>
<h2>Suivi de l'inventaire des églises</h2>

<div class="suiviInventaire container">

    <div class="row">
        <div class="col-md-12">
            <div id="cipar"></div>
            <table class="cipar_table">
            <?php
            $total = [];
            foreach($vs_statistiques_globales as $row):
                print "<tr>";
                foreach($row as $key=>$col):
                    print "<td>$col</td>\n";
                    $total[$key] += $col;
                endforeach;
                print "</tr>";
            endforeach;
            print "<tr>";
            foreach($total as $key=>$val) {
                print "<th>".($val >0 ? $val : "")."</th>\n";
            }
            print "</tr>";
            ?>
            </table>
            <table style="width:100%;">
                <tr><th>ID</th><th>Identifiant</th><th>Statut</th></tr>
                <?php
                foreach($va_eglises as $eglise):
                print "<tr><td>".$eglise["object_id"]."</td><td>".$eglise["idno"]."</td><td>".$eglise["status"]."</td></tr>";
                endforeach;
                ?>
            </table>
        </div>
    </div>

</div>
<div style="margin-bottom:100px;clear:both;"></div>

<style>
    h1 {
        text-transform: capitalize;
    }
    table.cipar_table {
        width:100%;
        margin:auto;
        margin-bottom:40px;

    }
    table tr:nth-child(2n+1) {
        background-color:lightgrey;
    }
    table td, table th {
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
            url: "<?php print __CA_URL_ROOT__; ?>/index.php/suiviInventaireEglises/Statistics/Json/diocese/<?php print $vs_diocese; ?>",
            dataType: "json",
            async: false
        }).responseText;

        var pie_options_large = {
            legend: 'none',
            pieSliceText: 'label',
            width: "100%", height: 240,
            isStacked: true
        };

        // Create our data table out of JSON data loaded from server.
        var data1 = new google.visualization.DataTable(jsonData1);

        // Instantiate and draw our chart, passing in some options.
        var chart1 = new google.visualization.BarChart(document.getElementById('cipar'));
        chart1.draw(data1, pie_options_large);

    }
</script>