<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

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
  $sejour->loadRefGHM();
  foreach ($sejour->_ref_operations as $keyOp => $valueOp) {
    $operation =& $sejour->_ref_operations[$keyOp];
    $operation->loadRefChir();
    $operation->loadRefPlageOp();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"       , $date       );
$smarty->assign("listSejours", $listSejours);

$smarty->display("vw_list_hospi.tpl");

?>
