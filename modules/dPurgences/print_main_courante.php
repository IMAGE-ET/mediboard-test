<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $g;

$date = CValue::getOrSession("date");

// Chargement des rpu de la main courante
$sejour = new CSejour;

$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$where = array();
$where["sejour.entree_reelle"] = "LIKE '$date%'";
$where["sejour.type"] = "= 'urg'";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$order = "sejour.entree_reelle ASC";

$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);
foreach($listSejours as &$curr_sejour) {
  $curr_sejour->loadRefsFwd();
  $curr_sejour->loadRefRPU();  
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date",$date);
$smarty->assign("listSejours", $listSejours);

$smarty->display("print_main_courante.tpl");

?>