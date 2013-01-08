<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();
$ds = CSQLDataSource::get("std");
// Récupération des paramètres
$chirSel   = CValue::getOrSession("chirSel");
$date      = CValue::getOrSession("date", mbDate());

$praticien = new CMediusers();
$praticien->load($chirSel);

$where = array();
$ljoin = array();
if ($praticien->isAnesth()) {
  $ljoin = array();
  $ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
  $ljoin["plagesop"]   = "operations.plageop_id = plagesop.plageop_id";
  $where[] = "operations.anesth_id = '$chirSel' OR (operations.anesth_id IS NULL AND plagesop.anesth_id = '$chirSel')";
} else {
  $where["sejour.praticien_id"] = "= '$chirSel'";
}
$where["sejour.entree"]   = "<= '$date 23:59:59'";
$where["sejour.sortie"]   = ">= '$date 00:00:00'";
$where["sejour.annule"]   = "= '0'";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$ljoin["affectation"] = "affectation.sejour_id = sejour.sejour_id";
$ljoin["lit"]         = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]     = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]     = "service.service_id = chambre.service_id";

$order = "`service`.`nom`, `chambre`.`nom`, `lit`.`nom`, `sejour`.`sortie` ASC, `sejour`.`entree` DESC";

$sejour = new CSejour();
$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);

foreach($listSejours as &$_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadRefsOperations();
  $_sejour->loadRefCurrAffectation($date);
  $_sejour->_ref_curr_affectation->loadRefLit();
  $_sejour->_ref_curr_affectation->_ref_lit->loadCompleteView();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"       , $date);
$smarty->assign("praticien"  , $praticien);
$smarty->assign("listSejours", $listSejours);

$smarty->display("inc_vw_hospi.tpl");

?>