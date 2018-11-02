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

 			if (!$this->request->user->canDoAction('can_use_statistics_viewer_plugin')) {
 				$this->response->setRedirect($this->request->config->get('error_display_url').'/n/3000?r='.urlencode($this->request->getFullUrlPath()));
 				return;
 			}

 			$vs_conf_file = $this->ops_plugin_path."/conf/".$this->ops_plugin_name.".conf";
 			if(is_file($vs_conf_file)) {
                $this->opo_config = Configuration::load($vs_conf_file);
            }

 		}

 		# -------------------------------------------------------
 		# Functions to render views
 		# -------------------------------------------------------
 		public function Index($type="") {
 			//$universe=$this->request->getParameter('universe', pString);
            //$this->view->setVar('statistics_listing', $this->opa_statistics[$universe]);
            $this->render('index_html.php');
 		}

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