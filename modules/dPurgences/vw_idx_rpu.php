<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$today = mbDate(mbGetValueFromGetOrSession("date", mbDate()));

$group = new CGroups();
$group->load($g);
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);

$sejour = new CSejour;
$where = array();
$where["entree_reelle"] = "LIKE '$today%'";
$where["type"] = "= 'urg'";
$order = "entree_reelle";
$listSejours = $sejour->loadList($where, $order);
foreach($listSejours as &$curr_sejour) {
  $curr_sejour->loadRefsFwd();
  $curr_sejour->loadRefRPU();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPrats"  , $listPrats);
$smarty->assign("listSejours", $listSejours);

$smarty->display("vw_idx_rpu.tpl");
?>