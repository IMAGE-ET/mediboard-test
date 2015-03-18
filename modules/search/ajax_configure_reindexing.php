<?php 

/**
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();
$error = "";

$search = new CSearch();
$search->createClient();
$settings["settings"] = CSearch::$settings_default;
$settings["settings"]["number_of_replicas"] = CAppUI::conf("search nb_replicas");

$types = "";
$group = CGroups::loadCurrent();
if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
  $types = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
}

$mapping = CSearch::$mapping_default;
$final_mapping = array();
foreach ($types as $type) {
  $final_mapping[$type]["properties"] = $mapping;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("error", $error);
$smarty->assign("settings", json_encode($settings));
$smarty->assign("types", $types);
$smarty->assign("mapping", json_encode($final_mapping));

$smarty->display("inc_configure_reindexing.tpl");