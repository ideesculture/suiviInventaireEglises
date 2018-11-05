<?php
/* ----------------------------------------------------------------------
 * plugins/statisticsViewer/controllers/StatisticsController.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This source code is free and modifiable under the terms of
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */

 	require_once(__CA_LIB_DIR__.'/core/TaskQueue.php');
 	require_once(__CA_LIB_DIR__.'/core/Configuration.php');
 	require_once(__CA_MODELS_DIR__.'/ca_lists.php');
 	require_once(__CA_MODELS_DIR__.'/ca_objects.php');
 	require_once(__CA_MODELS_DIR__.'/ca_object_representations.php');
 	require_once(__CA_MODELS_DIR__.'/ca_locales.php');
 	require_once(__CA_APP_DIR__.'/plugins/statisticsViewer/lib/statisticsSQLHandler.php');
error_reporting(E_ERROR);


class StatisticsController extends ActionController {
 		# -------------------------------------------------------
  		protected $opo_config,		// plugin configuration file
        $ops_plugin_name, $ops_plugin_path;


 		# -------------------------------------------------------
 		# Constructor
 		# -------------------------------------------------------

 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			global $allowed_universes;
 			
 			parent::__construct($po_request, $po_response, $pa_view_paths);

 			$this->ops_plugin_name = "suiviInventaireEglises";
 			$this->ops_plugin_path = __CA_APP_DIR__."/plugins/".$this->ops_plugin_name."Plugin";

 			$vs_conf_file = $this->ops_plugin_path."/conf/".$this->ops_plugin_name.".conf";
 			if(is_file($vs_conf_file)) {
                $this->opo_config = Configuration::load($vs_conf_file);
            }

 		}

 		# -------------------------------------------------------
 		# Functions to render views
 		# -------------------------------------------------------
 		public function Index($type="") {
            $o_data = new Db();
            $vs_query = "select CASE objects.status WHEN 0 THEN \"en attente\" WHEN 1 THEN \"en cours\" WHEN 2 THEN \"à valider\" WHEN 3 THEN \"validé\" ELSE \"valeur incohérente\" END as statut, count(*) as nombre from ca_objects as objects left join ca_objects as parents on parents.object_id=objects.parent_id left join ca_objects as grandsparents on parents.parent_id=grandsparents.object_id and grandsparents.type_id=261 WHERE objects.type_id = 262 and objects.deleted=0 and parents.type_id=23 and parents.parent_id is not null and grandsparents.object_id is not null GROUP BY objects.status;";
            $qr_result = $o_data->query($vs_query);
            $va_result = [];
            while($qr_result->nextRow()) {
                array_push($va_result, ["statut"=>$qr_result->get('statut'), "nombre"=>$qr_result->get('nombre')]);
            }
            $this->view->setVar('statistiques_globales', $va_result);
            $this->render('index_html.php');
 		}

        public function Eglises($type="") {
            //$universe=$this->request->getParameter('universe', pString);
            //$this->view->setVar('statistics_listing', $this->opa_statistics[$universe]);
            $diocese=$this->request->getParameter('diocese', pString);
            $ps_where = "";
            if($diocese != "") {
                $this->view->setVar('diocese', $diocese);
                $ps_where = " AND grandsparents.idno = \"".$diocese."\" ";
            }

            $o_data = new Db();
            $vs_query = "select CASE objects.status WHEN 0 THEN \"en attente\" WHEN 1 THEN \"en cours\" WHEN 2 THEN \"à valider\" WHEN 3 THEN \"validé\" ELSE \"valeur incohérente\" END as statut, count(*) as nombre from ca_objects as objects left join ca_objects as parents on parents.object_id=objects.parent_id left join ca_objects as grandsparents on parents.parent_id=grandsparents.object_id and grandsparents.type_id=261 WHERE objects.type_id = 262 and objects.deleted=0 and parents.type_id=23 and parents.parent_id is not null and grandsparents.object_id is not null $ps_where GROUP BY objects.status;";
            $qr_result = $o_data->query($vs_query);
            $va_result = [];
            while($qr_result->nextRow()) {
                $va_result[$qr_result->get('statut')] += $qr_result->get('nombre');
            }
            $this->view->setVar('totaux', $va_result);

            //$vs_query = "select objects.object_id, objects.idno, objects.status from ca_objects as objects left join ca_objects as parents on parents.object_id=objects.parent_id left join ca_objects as grandsparents on parents.parent_id=grandsparents.object_id and grandsparents.type_id=261 WHERE objects.type_id = 262 and objects.deleted=0 and parents.type_id=23 and parents.parent_id is not null and grandsparents.object_id is not null $ps_where order by 2;";
            $vs_query = "select grandsparents.idno as diocese, concat(\"<a href=/gestion/index.php/editor/objects/ObjectEditor/Summary/object_id/\",parents.object_id,\">\", parents.idno, \"</a> \",parentslabels.name) as fabrique, objects.object_id, concat(\"<a href=/gestion/index.php/editor/objects/ObjectEditor/Summary/object_id/\",objects.object_id,\">\", objects.idno, \"</a> \",labels.name) as idno, CASE objects.status WHEN 0 THEN \"en attente\" WHEN 1 THEN \"en cours\" WHEN 2 THEN \"à valider\" WHEN 3 THEN \"validé\" ELSE \"valeur incohérente\" END as statut from ca_objects as objects left join ca_objects as parents on parents.object_id=objects.parent_id left join ca_objects as grandsparents on parents.parent_id=grandsparents.object_id and grandsparents.type_id=261 left join ca_object_labels as parentslabels on parentslabels.object_id=parents.object_id and parentslabels.is_preferred=1 left join ca_object_labels as labels on labels.object_id=objects.object_id and labels.is_preferred=1 WHERE objects.type_id = 262 and objects.deleted=0 and parents.type_id=23 and parents.parent_id is not null and grandsparents.object_id is not null $ps_where order by 1,2,3,4;";
            //var_dump($vs_query);die();

            $qr_result = $o_data->query($vs_query);
            $va_result = [];
            while($qr_result->nextRow()) {
                array_push($va_result,
                    [
                        "diocese"=>$qr_result->get('diocese'),
                        "fabrique"=>$qr_result->get('fabrique'),
                        "object_id"=>$qr_result->get('object_id'),
                        "idno"=>$qr_result->get('idno'),
                        "status"=>$qr_result->get('statut')
                    ]
                );
            }
            $this->view->setVar('eglises', $va_result);
            $this->render('eglises_html.php');
        }


        public function Eglise() {
            $eglise_id=$this->request->getParameter('ID', pString);
            // Force for now
            //$eglise_id=387;
            $o_data = new Db();
            $vs_request = "select objects.object_id, medias.representation_id from ca_objects objects left join ca_objects_x_object_representations medias on objects.object_id=medias.object_id and medias.is_primary=1 where type_id=27 and deleted=0 and parent_id=$eglise_id";
            $qr_result = $o_data->query($vs_request);
            $vs_results = $qr_result->getAllRows();

            $this->view->setVar('eglise_id', $eglise_id);
            $this->view->setVar('objects_data', $vs_results);
            $this->render('eglise_html.php');
        }

        public function Suivi() {
            $o_data = new Db();
            $vs_request = "select object_id, idno from ca_objects where type_id=262";
            $qr_result = $o_data->query($vs_request);
            $objects_data = $qr_result->getAllRows();

            foreach($qr_result->getAllRows() as $objet) {
                // Force for now
                //$eglise_id=387;
                $vs_request1 = "select objects.object_id, medias.representation_id from ca_objects objects left join ca_objects_x_object_representations medias on objects.object_id=medias.object_id and medias.is_primary=1 where type_id=27 and deleted=0 and parent_id=".$objet["object_id"];
                $qr_result1 = $o_data->query($vs_request1);
                $objects_data = $qr_result1->getAllRows();

                $num_completed=0;
                $num_objets = sizeof($objects_data);

                // First pass : computing
                foreach($objects_data as $object) {
                    if (!$object["representation_id"]) continue;
                    $num_completed++;
                }
                print $objet["idno"]." ".($num_objets > 0 ? round($num_completed/$num_objets*100,2) : 0)."\n";

                $o_data->query("REPLACE INTO acf_suivi_avancement (eglise_id, avancement) VALUES (".$objet["object_id"].",".($num_objets > 0 ? round($num_completed/$num_objets*100,2) : 0).")");
            }

            print json_encode(["results"=>"OK"]);
            die();
        }

        public function JaiFini() {
            $eglise_id=$this->request->getParameter('ID', pString);
            $this->view->setVar("eglise_id", $eglise_id);
            $this->render('jaifini_confirmation_html.php');
        }


        public function DemandeValidation() {
            $eglise_id=$this->request->getParameter('ID', pString);
            $this->view->setVar("eglise_id", $eglise_id);
            $this->render('demande_validation_html.php');
        }
        /*
         * Json based views
         */

 		public function JsonDioceses() {
            $o_data = new Db();
            $qr_result = $o_data->query("select grandsparents.idno, CASE objects.status WHEN 0 THEN \"en attente\" WHEN 1 THEN \"en cours\" WHEN 2 THEN \"à valider\" WHEN 3 THEN \"validé\" ELSE \"valeur incohérente\" END as statut, count(*) as nombre from ca_objects as objects left join ca_objects as parents on parents.object_id=objects.parent_id left join ca_objects as grandsparents on parents.parent_id=grandsparents.object_id and grandsparents.type_id=261 where objects.type_id = 262 and objects.deleted=0 and parents.type_id=23 and parents.parent_id is not null and grandsparents.object_id is not null group by parents.parent_id, objects.status;");
            $first=1;
            print "[";
            while($qr_result->nextRow()) {
                if(!$first) print ",";
                print "{\"idno\":\"".$qr_result->get('idno')."\",\n";
                print "\"statut\":\"".$qr_result->get('statut')."\",\n";
                print "\"nombre\":\"".$qr_result->get('nombre')."\"}\n";
                $first=0;
            }
            print "]\n";
            exit;
        }

        public function Json() {
            $diocese=$this->request->getParameter('diocese', pString);
            $ps_where = "";
            if($diocese != "") $ps_where = " AND grandsparents.idno = \"".$diocese."\" ";

            $o_data = new Db();
            $vs_query = "select CASE objects.status WHEN 0 THEN \"en attente\" WHEN 1 THEN \"en cours\" WHEN 2 THEN \"à valider\" WHEN 3 THEN \"validé\" ELSE \"valeur incohérente\" END as statut, count(*) as nombre from ca_objects as objects left join ca_objects as parents on parents.object_id=objects.parent_id left join ca_objects as grandsparents on parents.parent_id=grandsparents.object_id and grandsparents.type_id=261 WHERE objects.type_id = 262 and objects.deleted=0 and parents.type_id=23 and parents.parent_id is not null and grandsparents.object_id is not null $ps_where GROUP BY objects.status;";
            $qr_result = $o_data->query($vs_query);
            $va_result = [];
            while($qr_result->nextRow()) {
                array_push($va_result, ["statut"=>$qr_result->get('statut'), "nombre"=>$qr_result->get('nombre')]);
            }
            print $this->arrayToGoogleDataTable($va_result);
            exit;
        }

        /*
         *   Private functions
         */


        /*  Google for dataviz requires this particular form of JSON
            https://developers.google.com/chart/interactive/docs/php_example
            The goal is to obtain this :
            {
              "cols": [
                    {"id":"","label":"Topping","pattern":"","type":"string"},
                    {"id":"","label":"Slices","pattern":"","type":"number"}
                  ],
              "rows": [
                    {"c":[{"v":"Mushrooms","f":null},{"v":3,"f":null}]},
                    {"c":[{"v":"Onions","f":null},{"v":1,"f":null}]},
                    {"c":[{"v":"Olives","f":null},{"v":1,"f":null}]},
                    {"c":[{"v":"Zucchini","f":null},{"v":1,"f":null}]},
                    {"c":[{"v":"Pepperoni","f":null},{"v":2,"f":null}]}
                  ]
            }
         */

        private function arrayToGoogleDataTable($array) {
 		    $first_row = current($array);
 		    $keys = array_keys($first_row);
 		    $result="";
            $result .= "{\"cols\": [\n";
 		    foreach($keys as $num=>$key) {
 		        if($num != 0) $result.=",\n";
                $result .=  "\t{\"id\":\"\",\"label\":\"".$key."\",\"pattern\":\"\",\"type\":\"".($num == 0 ? "string" : "number")."\"}";
            }
            $result .=  "\n],\n\"rows\": [\n";

 		    foreach($array as $num=>$row) {
                if($num != 0) $result.=",\n";
                $num2 = 0;
                $result .=  "\t{\"c\":[";
 		        foreach($row as $key=>$value) {
                    if($num2 != 0) $result.=",";
                    if($this->is_digit($value)) {
                        $value = $value*1;
                    }
                        else {
                        $value = "\"".$value."\"";
                    }

                    $result .= "{\"v\":".$value.",\"f\":null}";
                    $num2++;
                }
                $result .= "]}";
            }
            $result .=  "\n]}";
 		    return $result;
        }

        // Simple is_digit function
        private function is_digit($digit) {
            if(is_int($digit)) {
                return true;
            } elseif(is_string($digit)) {
                return ctype_digit($digit);
            } else {
                // booleans, floats and others
                return false;
            }
        }

 	}
 ?>