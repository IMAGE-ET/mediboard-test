<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass("dPplanningOp", "sejour"));

$date = mbGetValueFromGetOrSession("date", mbDate());
$next = mbDate("+1 day", $date);

$sejour = new CSejour;
$where = array();
$where["entree_prevue"] = "< '$next'";
$where["sortie_prevue"] = "> '$date'";
$order = array();
$order[] = "sortie_prevue";
$order[] = "entree_prevue";

$listSejours = $sejour->loadList($where, $order);

foreach ($listSejours as $keySejour => $valueSejour) {
  $sejour =& $listSejours[$keySejour];
  $sejour->loadRefs();
  foreach ($sejour->_ref_operations as $keyOp => $valueOp) {
    $operation =& $sejour->_ref_operations[$keyOp];
    $operation->loadRefChir();
    $operation->loadRefPlageOp();
    $operation->loadRefGHM();
  }
}

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign("date", $date);
$smarty->assign("listSejours", $listSejours);

$smarty->display('vw_list_hospi.tpl');

?>
