<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers", "functions"));
require_once($AppUI->getModuleClass("dPhospi", "service"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$date = mbGetValueFromGetOrSession("date", mbDate()); 
$firstDayOfWeek = mbDate("last sunday", $date);
$firstDayOfWeek = mbDate("+ 1 day", $firstDayOfWeek);
$nextDay = mbDate("+ 7 days", $firstDayOfWeek);
$prevDay = mbDate("- 7 days", $firstDayOfWeek);

$mainTab = array();
$listDays = array();
$listSpec = new CFunctions;
$functions = $listSpec->loadSpecialites(PERM_READ);

// Initialisation
$curr_day = $firstDayOfWeek;
for($i=0; $i<7; $i++){
  $listDays[] = $curr_day;
  $mainTab["allocated"]["functions"][0]["text"] = "Total placés";
  $mainTab["allocated"]["functions"][0]["class"] = "groupcollapse";
  $mainTab["allocated"]["functions"][0]["color"] = null;
  $mainTab["allocated"]["functions"][0]["days"]["$curr_day"]["nombre"] = 0;
  $mainTab["notallocated"]["functions"][0]["text"] = "Total à placer";
  $mainTab["notallocated"]["functions"][0]["class"] = "groupcollapse";
  $mainTab["notallocated"]["functions"][0]["days"]["$curr_day"]["nombre"] = 0;
  $mainTab["notallocated"]["functions"][0]["color"] = null;
  $curr_day = mbDate("+ 1 day", $curr_day);
}

// Parcours des spécialités
foreach($functions as $value){
  $curr_day = $firstDayOfWeek;
  $function = $value->function_id;
  $mainTab["allocated"]["functions"][$function]["text"] = $value->text;
  $mainTab["allocated"]["functions"][$function]["class"] = "allocated";
  $mainTab["allocated"]["functions"][$function]["color"] = $value->color;
  $mainTab["notallocated"]["functions"][$function]["text"] = $value->text;
  $mainTab["notallocated"]["functions"][$function]["class"] = "notallocated";
  $mainTab["notallocated"]["functions"][$function]["color"] = $value->color;

  // Parcours des jours
  for($i=0; $i<7; $i++) {
  	// Interventions affectées
    $sql = "SELECT COUNT(operations.operation_id) AS total
            FROM `operations`
            LEFT JOIN `plagesop`
            ON plagesop.id = operations.plageop_id
            LEFT JOIN `affectation`
            ON affectation.operation_id = operations.operation_id
            LEFT JOIN `users_mediboard`
            ON users_mediboard.user_id = operations.chir_id
            WHERE '$curr_day' BETWEEN operations.date_adm AND ADDDATE(operations.date_adm, INTERVAL operations.duree_hospi DAY)
            AND '$curr_day' BETWEEN affectation.entree AND affectation.sortie
            AND users_mediboard.function_id = '$function'
            AND operations.annulee = 0";
    $result = db_loadList($sql);
    $mainTab["allocated"]["functions"][$function]["days"]["$curr_day"]["nombre"] = $result[0]["total"];
    $mainTab["allocated"]["functions"][0]["days"]["$curr_day"]["nombre"] += $result[0]["total"];

    // Interventions non affectées
    $sql = "SELECT COUNT(operations.operation_id) AS total
            FROM `operations`
            LEFT JOIN `plagesop`
            ON plagesop.id = operations.plageop_id
            LEFT JOIN `affectation`
            ON affectation.operation_id = operations.operation_id
            LEFT JOIN `users_mediboard`
            ON users_mediboard.user_id = operations.chir_id
            WHERE '$curr_day' BETWEEN operations.date_adm AND ADDDATE(operations.date_adm, INTERVAL operations.duree_hospi DAY)
            AND affectation.affectation_id IS NULL
            AND users_mediboard.function_id = '$function'
            AND operations.annulee = 0";
    $result = db_loadList($sql);
    $mainTab["notallocated"]["functions"][$function]["days"]["$curr_day"]["nombre"] = $result[0]["total"];
    $mainTab["notallocated"]["functions"][0]["days"]["$curr_day"]["nombre"] += $result[0]["total"];
    
    $curr_day = mbDate("+ 1 day", $curr_day);
  }
}

$mainTab["busy"][$curr_day] = array();
$mainTab["free"][$curr_day] = array();

// Création du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('nextDay' , $nextDay);
$smarty->assign('prevDay' , $prevDay);
$smarty->assign('listDays' , $listDays);
$smarty->assign('mainTab' , $mainTab);

$smarty->display('vw_recapitulatif.tpl');

?>