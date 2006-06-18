<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Date du jour
$today = date("Y-m-d");

$where = array();
$where["plageop_id"] = "IS NULL";
$where["patient_id"] = "IS NOT NULL";
$where["annulee"] = "= 0";
$ljoin = array();
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["sejour"] = "operations.sejour_id = sejour.sejour_id";
$order = "sejour.entree_prevue, patients.nom, patients.prenom";

// Récupération urgences de chaque jour
// Admissions passées
for ($i = -5; $i < 0; $i++) {
  $curr_day = mbDate("$i DAYS", $today);
  $next_day = mbDate("+ 1 DAY", $curr_day);

  $operation = new COperation;
  $where["entree_prevue"] = "BETWEEN '$curr_day' and '$next_day'";
  
  $list1[$i]["date"] = $curr_day;
  $list1[$i]["urgences"] = $operation->loadList($where, $order, null, null, $ljoin);
  foreach($list1[$i]["urgences"] as $key => $value) {
    $list1[$i]["urgences"][$key]->loadRefsFwd();
    $list1[$i]["urgences"][$key]->_ref_sejour->loadRefsFwd();
  }
}

//Admissions à venir
for ($i = 0; $i < 5; $i++) {
  $curr_day = mbDate("$i DAYS", $today);
  $next_day = mbDate("+ 1 DAY", $curr_day);
  
  $operation = new COperation;
  $where["entree_prevue"] = "BETWEEN '$curr_day' and '$next_day'";
  
  $list2[$i]["date"] = $curr_day;
  $list2[$i]["urgences"] = $operation->loadList($where, $order, null, null, $ljoin);
  foreach($list2[$i]["urgences"] as $key => $value) {
    $list2[$i]["urgences"][$key]->loadRefsFwd();
    $list2[$i]["urgences"][$key]->_ref_sejour->loadRefsFwd();
  }
}

// Création du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;
$smarty->assign('list1' , $list1 );
$smarty->assign('list2' , $list2 );

$smarty->display('vw_urgences.tpl');

?>