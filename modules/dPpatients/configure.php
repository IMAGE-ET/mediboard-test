<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::Admin();

$types_antecedents_active     = explode('|', CAppUI::conf("dPpatients CAntecedent types"));
$appareils_antecedents_active = explode('|', CAppUI::conf("dPpatients CAntecedent appareils"));

$departements = array();
for ($i = 1 ; $i < 96 ; $i++) {
  $departements[] = str_pad($i, 2, "0", STR_PAD_LEFT);
}

$service = new CService;
$services = $service->loadGroupList();

foreach($services as $_service) {
	$_service->_conf_object = $_service->loadUniqueBackRef("config_constantes_medicales");
}

$group = CGroups::loadCurrent();
$group->_conf_object = $group->loadUniqueBackRef("config_constantes_medicales");

$base = new CConfigConstantesMedicales;
$where = array(
  "group_id" => "IS NULL",
  "service_id" => "IS NULL",
);
$base->loadObject($where);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("pass", CValue::get("pass"));
$smarty->assign("types_antecedents", CAntecedent::$types);
$smarty->assign("types_antecedents_active", $types_antecedents_active);
$smarty->assign("appareils_antecedents", CAntecedent::$appareils);
$smarty->assign("appareils_antecedents_active", $appareils_antecedents_active);
$smarty->assign("departements", $departements);
$smarty->assign("base", $base);
$smarty->assign("group", $group);
$smarty->assign("services", $services);
$smarty->display("configure.tpl");

?>