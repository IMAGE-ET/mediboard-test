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

$where = array();
$where["sejour.praticien_id"] = "= '$chirSel'";
$where["sejour.entree"]   = "<= '$date 23:59:59'";
$where["sejour.sortie"]   = ">= '$date 00:00:00'";
$where["sejour.annule"]   = "= '0'";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$ljoin = array();
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
$smarty->assign("listSejours", $listSejours);

$smarty->display("inc_vw_hospi.tpl");

?>