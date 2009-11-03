<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Thomas Despoix
*/

global $can;

$can->needsAdmin();

// Creation d'une banque
$banque = new CBanque();
$banque_id = CValue::getOrSession("banque_id");

$order = "nom ASC";
$banques = $banque->loadList(null, $order);

// Chargement de la banque selectionnée
if ($banque_id){
  $banque->load($banque_id);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("banque"    , $banque);
$smarty->assign("banques"   , $banques);

$smarty->display("vw_banques.tpl");
?>