<?php
//$va_statistics 	= $this->getVar('statistics_listing');
define("__ASSET_IMAGE_DIR__", __DIR__."/images");
define("__ASSET_IMAGE_URL__", __CA_URL_ROOT__."/app/plugins/suiviInventaireEglises/views/images");
$vs_statistiques_globales = $this->getVar("statistiques_globales");
$va_totaux = $this->getVar("totaux");
$vn_totaux = array_sum($va_totaux);
$va_eglises = $this->getVar("eglises");
$vs_diocese = $this->getVar("diocese");
//var_dump($vs_statistiques_globales);die();
?>

<?php
    // https://datatables.net
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<h1><?php print $vs_diocese; ?></h1>
<h2>Suivi de l'inventaire des églises</h2>

<div class="suiviInventaire container">

    <div class="row">
        <span class="col-md-12">
            <div id="global_profession">
            <?php
            foreach($va_totaux as $statut=>$value):
                print "<div class='".$statut." progression' style='width:".round($value*100/$vn_totaux)."%;'></div>";
            endforeach;
            ?>
            </div>
            <?php foreach($va_totaux as $statut=>$value): ?>
                <span class="<?php print $statut; ?> legende"></span> <?php print $statut; ?>
            <?php endforeach; ?>
            <table id="eglises_table" style="width:100%;">
                <thead><tr><th>Diocèse</th><th>Fabrique</th><th>Église</th><th>Statut</th><th>Avancement</th></tr></thead>
                <tbody>
                <?php
                foreach($va_eglises as $eglise):
                print "<tr><td>"
                    .strtoupper($eglise["diocese"])
                    ."</td><td>"
                    .$eglise["fabrique"]
                    ."</td><td>"
                    .$eglise["idno"]
                    ."</td><td>"
                    .$eglise["status"]
                    ."</td><td>"
                    ."<a href="
                    .__CA_URL_ROOT__
                    ."/index.php/suiviInventaireEglises/Statistics/Eglise/ID/"
                    .$eglise["object_id"]
                    .">Voir</a></td></tr>";
                endforeach;
                ?>
                </tbody>
            </table>
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
        $('#eglises_table').DataTable({
            "pageLength": 25,
            "dom": 'Bfrtip',
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "language":{
                "sProcessing":     "Traitement en cours...",
                "sSearch":         "Rechercher&nbsp;:",
                "sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
                "sInfo":           "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                "sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
                "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                "sInfoPostFix":    "",
                "sLoadingRecords": "Chargement en cours...",
                "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
                "oPaginate": {
                    "sFirst":      "Premier",
                        "sPrevious":   "Pr&eacute;c&eacute;dent",
                        "sNext":       "Suivant",
                        "sLast":       "Dernier"
                },
                "oAria": {
                    "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
                },
                "select": {
                    "rows": {
                        _: "%d lignes séléctionnées",
                            0: "Aucune ligne séléctionnée",
                            1: "1 ligne séléctionnée"
                    }
                }
            }
        });
    });
</script>