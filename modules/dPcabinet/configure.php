<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Thomas Despoix
*/

global $can;

$can->needsAdmin();

// Creation d'une banque
$banque = new CBanque();
$banque_id = mbGetValueFromGetOrSession("banque_id");

// Recuperation de la liste des banques
$banques = $banque->loadList();

// Chargement de la banque selectionnée
if($banque_id){
  $banque->load($banque_id);
}

$hours = range(0, 23);
$intervals = array("5","10","15","20","30");


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("banque"    , $banque);
$smarty->assign("banques"   , $banques);
$smarty->assign("hours"     , $hours);
$smarty->assign("intervals" , $intervals);

$smarty->display("configure.tpl");
?>