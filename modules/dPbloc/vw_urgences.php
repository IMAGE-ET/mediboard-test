<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// Date du jour
$today = date("Y-m-d");

$where = array();
$where["plageop_id"] = "IS NULL";
$where["annulee"] = "= '0'";
$order = "operations.date, operations.time_operation";

// Récupération urgences de chaque jour
// Admissions passées
for ($i = -5; $i < 0; $i++) {
  $curr_day = mbDate("$i DAYS", $today);
  //$next_day = mbDate("+ 1 DAY", $curr_day);

  $operation = new COperation;
  $where["date"] = "= '$curr_day'";
  
  $list1[$i]["date"] = $curr_day;
  $list1[$i]["urgences"] = $operation->loadList($where, $order);
  foreach($list1[$i]["urgences"] as $key => $value) {
    $list1[$i]["urgences"][$key]->loadRefsFwd();
    $list1[$i]["urgences"][$key]->_ref_sejour->loadRefsFwd();
  }
}

//Admissions à venir
for ($i = 0; $i < 5; $i++) {
  $curr_day = mbDate("$i DAYS", $today);
  //$next_day = mbDate("+ 1 DAY", $curr_day);
  
  $operation = new COperation;
  $where["date"] = "= '$curr_day'";
  
  $list2[$i]["date"] = $curr_day;
  $list2[$i]["urgences"] = $operation->loadList($where, $order);
  foreach($list2[$i]["urgences"] as $key => $value) {
    $list2[$i]["urgences"][$key]->loadRefsFwd();
    $list2[$i]["urgences"][$key]->_ref_sejour->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list1" , $list1 );
$smarty->assign("list2" , $list2 );

$smarty->display("vw_urgences.tpl");

?>