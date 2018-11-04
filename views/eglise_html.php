<?php
//$va_statistics 	= $this->getVar('statistics_listing');
define("__ASSET_IMAGE_DIR__", __DIR__."/images");
define("__ASSET_IMAGE_URL__", __CA_URL_ROOT__."/app/plugins/suiviInventaireEglises/views/images");
$eglise_id = $this->getVar("eglise_id");

$eglise = new ca_objects($eglise_id);
$objects_data = $this->getVar("objects_data");
$objects_completion_dates = [];
$creation_dates = [];
$num_objets = sizeof($objects_data);
$num_irpa=0;
$num_completed=0;

// First pass : computing
foreach($objects_data as $object) {
    //$array()
    $vt_object = new ca_objects($object["object_id"]);
    $creation_info = $vt_object->get("ca_objects.created", ["returnAsArray"=>true]);
    $creation_dates[] = $creation_info["timestamp"];
    if (!$object["representation_id"]) continue;

    $media = new ca_object_representations($object["representation_id"]);
    $irpa_ref = $vt_object->get("ca_objects.irpa");
    if(!$irpa_ref) $num_irpa++;
    $media_creation_info = $media->get("ca_object_representations.created", ["returnAsArray"=>true]);
    $objects_completion_dates[] = $media_creation_info["timestamp"];
    $num_completed++;
}
sort($objects_completion_dates);
sort($creation_dates);


print "<script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>";
print "
    <script type=\"text/javascript\">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('datetime', 'Date');
        data.addColumn('number', 'Objets avec photos');
";
$count=0;
foreach($objects_completion_dates as $value) {
    print "
        data.addRow([new Date(".($value*1000)."), ".$count++."]);";
    //$count++;
}
print "        
        var data2 = new google.visualization.DataTable();
        data2.addColumn('datetime', 'Date');
        data2.addColumn('number', 'Fiches objets');
";

$count=0;
foreach($creation_dates as $value) {
    print "
        data2.addRow([new Date(".($value*1000)."), ".$count++."]);";
}

$min_timestamp = min([reset($creation_dates), reset($objects_completion_dates)]);
$max_timestamp = max([end($creation_dates), end($objects_completion_dates)]);

print "        
        var data3 = new google.visualization.DataTable();
        data3.addColumn('datetime', 'Date');
        data3.addColumn('number', 'Objets IRPA');
        data3.addRow([new Date(".($min_timestamp*1000)."), ".$num_irpa."]);
        data3.addRow([new Date(".($max_timestamp*1000)."), ".$num_irpa."]);";

print "
        var joinedData = google.visualization.data.join(data, data2, 'full', [[0, 0]], [1], [1]);
        
        var joinedData = google.visualization.data.join(data, data2, 'full', [[0, 0]], [1], [1]);
        var joinedData2 = google.visualization.data.join(joinedData, data3, 'full', [[0, 0]], [1,2], [1]);
        
        var options = {
          vAxis: {minValue: 0},
          interpolateNulls: true,
          legend: {position: 'bottom', maxLines: 3},
          chartArea: {left:50,right:50, top:20, bottom:50},
          hAxis:{      
            format: 'dd/MM/YY',
            labelAngle: -50
          },
          series: {
            0: {
                areaOpacity: 0.8,
                color: '#58A668'
            },
            1: {
                areaOpacity: 0.1,
                color: '#6EBDCE'
            },
            2: {
                areaOpacity: 0,
                color: '#333333'
            }
          }
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(joinedData2, options);
      }
    </script>";
?>

<h1><?php print $eglise->get("ca_objects.preferred_labels.name")." <small>".$eglise->get("ca_objects.idno")."</small>"; ?> </h1>
<h2>Suivi de l'inventaire</h2>

<div class="suiviInventaire container" style="font-size:1.2em">

    <div class="row">
        <span class="col-md-12">
            <div id="chart_div" style="width: 100%; height: 400px;"></div>
    </div>
    <div class="row" style="margin-top:40px;">
        <div style="width:50%;padding:1% 5%;float:left;">
            <b>A ce jour</b> :
        <ul>
            <li><?php print $num_objets; ?> objets</li>
            <li><?php print $num_irpa; ?> objets liés à une fiche IRPA (<?php print round($num_irpa/$num_objets*100, 2); ?>% des objets)</li>
            <li><?php print $num_irpa; ?> objets liés à une fiche IRPA (<?php print round($num_irpa/$num_objets*100, 2); ?>% des objets)</li>
            <li><?php print $num_completed; ?> objets considérés comme inventoriés<sup>*</sup> (<?php print round($num_completed/$num_objets*100, 2); ?>% des objets)
            <br><small><span>*</span> avec au moins une image associée</small></li>

        </ul>
        </div>
        <div style="width:30%;padding:2%;background-color:RGBA(88, 166, 104, <?php print round($num_completed/$num_objets*100); ?>);border-radius:20px;float:left;height:100px;">
            <div style="line-height: 90px;text-align: center;width:100%;font-size:60px;color:white;"><?php print round($num_completed/$num_objets*100); ?><small>%</small></div>
        </div>
    </div>

</div>
<div style="margin-bottom:100px;clear:both;"></div>

<style>
    h1 {
        text-transform: capitalize;
    }
    #global_profession {
        background-color:darkgrey;
    }
    .progression {
        height:6px;
        background-color: red;
        margin-bottom: 10px;
        float:left;
    }
    .legende {
        height:10px;
        width:10px;
        display:inline-block;
    }
    .en.attente {
        background-color: #3D3D3D;
    }
    .en.cours {
        background-color: #00b3ee;
    }
    #eglises_table {
        margin-top:10px;
    }
    #eglises_table_wrapper {
        margin-top:40px;
    }
    .dt-buttons {
        display: inline-block;
    }
</style>

<script>
    $(document).ready(function() {
        // assumes you have timestamps in column 0, and two data series (columns 1 and 2)
        /*var view = new google.visualization.DataView(data);
        view.setColumns([{
            type: 'date',
            label: data.getColumnLabel(0),
            calc: function (dt, row) {
                var timestamp = dt.getValue(row, 0) * 1000; // convert to milliseconds
                return new Date(timestamp);
            }
        }, 1, 2]);*/
    });
</script>