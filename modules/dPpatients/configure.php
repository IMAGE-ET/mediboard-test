<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::Admin();

// Types et Appareils
$active_types     = explode('|', CAppUI::conf("dPpatients CAntecedent types"    ));
$active_appareils = explode('|', CAppUI::conf("dPpatients CAntecedent appareils"));
$all_types     = array_unique(array_merge(CAntecedent::$types    , $active_types    ));
$all_appareils = array_unique(array_merge(CAntecedent::$appareils, $active_appareils));

// Départements des correspondants
$departements = array();
for ($i = 1 ; $i < 96 ; $i++) {
  $departements[] = str_pad($i, 2, "0", STR_PAD_LEFT);
}

// Ajout des DOM-TOM
$departements[] = "CS"; // Corse du Sud
$departements[] = "GD"; // Guadeloupe
$departements[] = "GY"; // Guyanne
$departements[] = "HC"; // Haute Corse
$departements[] = "MA"; // Martinique
$departements[] = "MY"; // Mayotte
$departements[] = "PS"; // Nouvelle Calédonie
$departements[] = "PF"; // Polynésie française
$departements[] = "RE"; // Réunion
$departements[] = "PM"; // Saitn Pierre et Miquelon
$departements[] = "WF"; // Wallis et Futuna

// Services
$service = new CService;
$services = $service->loadGroupList();
foreach ($services as $_service) {
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

// Nombre de patients
$patient = new CPatient();
$nb_patients = $patient->countList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("active_types"    , $active_types);
$smarty->assign("active_appareils", $active_appareils);
$smarty->assign("all_types"       , $all_types);
$smarty->assign("all_appareils"   , $all_appareils);

$smarty->assign("pass"        , CValue::get("pass"));
$smarty->assign("departements", $departements);
$smarty->assign("base"        , $base);
$smarty->assign("group"       , $group);
$smarty->assign("services"    , $services);

$smarty->assign("nb_patients", $nb_patients);

$smarty->display("configure.tpl");

?>