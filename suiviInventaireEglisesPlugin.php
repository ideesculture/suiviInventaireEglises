<?php
/* ----------------------------------------------------------------------
 * mediaImportPlugin.php : 
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
 
	class suiviInventaireEglisesPlugin extends BaseApplicationPlugin {
		# -------------------------------------------------------
		protected $description = "Plugin Suivi de l'inventaire des églises";
		# -------------------------------------------------------
		private $opo_config;
		private $ops_plugin_path;
		# -------------------------------------------------------
		public function __construct($ps_plugin_path) {
			$this->ops_plugin_path = $ps_plugin_path;
			$this->description = _t("Suivi de l'inventaire des églises");
			parent::__construct();
			$this->opo_config = Configuration::load($ps_plugin_path.'/conf/suiviInventaireEglises.conf');
		}
		# -------------------------------------------------------
		/**
		 * Override checkStatus() to return true - the statisticsViewerPlugin always initializes ok... (part to complete)
		 */
		public function checkStatus() {
			return array(
				'description' => $this->getDescription(),
				'errors' => array(),
				'warnings' => array(),
				'available' => true
			);
		}
		# -------------------------------------------------------
		/**
		 * Insert activity menu
		 */
		public function hookRenderMenuBar($pa_menu_bar) {
			if ($o_req = $this->getRequest()) {
				//if (!$o_req->user->canDoAction('can_use_media_import_plugin')) { return true; }
				
				if (isset($pa_menu_bar['suiviInventaireEglises_menu'])) {
					$va_menu_items = $pa_menu_bar['suiviInventaireEglises_menu']['navigation'];
					if (!is_array($va_menu_items)) { $va_menu_items = array(); }
				} else {
					$va_menu_items = array();
				}


                $va_menu_items[1] = array(
                    'displayName' => "Indicateurs",
                    'requires' => array(), //array('action:can_use_statistics_viewer_plugin' => 'AND'),
                    "default" => array(
                        'module' => 'suiviInventaireEglises',
                        'controller' => 'Statistics',
                        'action' => 'Index'
                    )
                );

                $pa_menu_bar['suiviInventaire_menu'] = array(
					'displayName' => _t('Suivi'),
					'navigation' => $va_menu_items,
					'requires' => array() //array('action:can_use_statistics_viewer_plugin' => 'AND')
				);
			}

			return $pa_menu_bar;
		}
		# -------------------------------------------------------
		/**
		 * Add plugin user actions
		 */
		static function getRoleActionList() {
			return array();
		}
		
	}
?>
