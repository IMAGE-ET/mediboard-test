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

// Récupération urgences de chaque jour
// Admissions passées
for($i = -5; $i < 0; $i++) {
  $curr_day = mbDate("+ $i DAYS", $today);
  $list1[$i]["date"] = $curr_day;
  $list1[$i]["urgences"] = new COperation;
  $where = array();
  $where["date_adm"] = "= '$curr_day'";
  $where["plageop_id"] = "IS NULL";
  $where["pat_id"] = "IS NOT NULL";
  $where["annulee"] = "= 0";
  $ljoin = array();
  $ljoin["patients"] = "operations.pat_id = patients.patient_id";
  $order = "operations.time_adm, patients.nom, patients.prenom";
  $list1[$i]["urgences"] = $list1[$i]["urgences"]->loadList($where, $order, null, null, $ljoin);
  foreach($list1[$i]["urgences"] as $key => $value) {
    $list1[$i]["urgences"][$key]->loadRefsFwd();
  }
}

//Admissions à venir
for($i = 0; $i < 5; $i++) {
  $curr_day = mbDate("+ $i DAYS", $today);
  $list2[$i]["date"] = $curr_day;
  $list2[$i]["urgences"] = new COperation;
  $where = array();
  $where["date_adm"] = "= '$curr_day'";
  $where["plageop_id"] = "IS NULL";
  $where["pat_id"] = "IS NOT NULL";
  $ljoin = array();
  $ljoin["patients"] = "operations.pat_id = patients.patient_id";
  $order = "operations.time_adm, patients.nom, patients.prenom";
  $list2[$i]["urgences"] = $list2[$i]["urgences"]->loadList($where, $order, null, null, $ljoin);
  foreach($list2[$i]["urgences"] as $key => $value) {
    $list2[$i]["urgences"][$key]->loadRefsFwd();
  }
}

// Création du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;
$smarty->assign('list1' , $list1 );
$smarty->assign('list2' , $list2 );

$smarty->display('vw_urgences.tpl');

?>