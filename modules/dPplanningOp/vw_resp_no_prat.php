<?php /* $Id: vw_sortie_rpu.php 6854 2009-09-03 16:16:08Z lryo $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: 6854 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can, $m;

$can->needsAdmin();

$group = CGroups::loadCurrent();


$sejour_no_prat = array();
$sejour = new CSejour();

$ljoin = array();
$ljoin["users"] = "`users`.`user_id` = `sejour`.`praticien_id`";
  
$where = array();
// Type non Praticien, Anesthésiste, Medecin
$where["users.user_type"] = " != '13' AND users.user_type != '3' AND users.user_type != '4'";

$order = "sejour.sortie_reelle DESC";

$sejours = $sejour->loadList($where, $order, null, null, $ljoin);
foreach ($sejours as $_sejour) {
  $_sejour->loadRefPraticien();
  $sejour_no_prat[$_sejour->_ref_praticien->_id][] = $_sejour;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour_no_prat", $sejour_no_prat);
$smarty->assign("sejours"       , $sejours);

$smarty->display("vw_resp_no_prat.tpl");
?>