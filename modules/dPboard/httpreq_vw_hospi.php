<?php

/**
 * dPboard
 *
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();
$ds = CSQLDataSource::get("std");
// Récupération des paramètres
$chirSel   = CValue::getOrSession("chirSel");
$date      = CValue::getOrSession("date", CMbDT::date());

$praticien = new CMediusers();
$praticien->load($chirSel);

$where = array();
$ljoin = array();
if ($praticien->isAnesth()) {
  $ljoin = array();
  $ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
  $ljoin["plagesop"]   = "operations.plageop_id = plagesop.plageop_id";
  $where[] = "operations.anesth_id = '$chirSel' OR (operations.anesth_id IS NULL AND plagesop.anesth_id = '$chirSel')";
}
else {
  $where["sejour.praticien_id"] = "= '$chirSel'";
}
$where["sejour.entree"]   = "<= '$date 23:59:59'";
$where["sejour.sortie"]   = ">= '$date 00:00:00'";
$where["sejour.annule"]   = "= '0'";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$sejour = new CSejour();
/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, null, null, null, $ljoin);

CStoredObject::massLoadFwdRef($listSejours, "patient_id");
foreach ($listSejours as $_sejour) {
  $_sejour->loadRefPraticien();
  $_sejour->loadRefPatient();
  $_sejour->loadRefsOperations();
  $_sejour->loadRefCurrAffectation("$date ".CMbDT::time());
  $_sejour->_ref_curr_affectation->loadRefLit();
  $_sejour->_ref_curr_affectation->_ref_lit->loadCompleteView();
}

$lits = CMbArray::pluck($listSejours, "_ref_curr_affectation", "_ref_lit");

$sorter_chambre       = CMbArray::pluck($lits, "_ref_chambre", "_view");
$sorter_service       = CMbArray::pluck($lits, "_ref_chambre", "_ref_service", "_view");
$sorter_lit           = CMbArray::pluck($lits, "_view");
$sorter_sejour_sortie = CMbArray::pluck($listSejours, "sortie");
$sorter_sejour_entree = CMbArray::pluck($listSejours, "entree");

array_multisort(
  $sorter_service, SORT_ASC,
  $sorter_chambre, SORT_ASC,
  $sorter_lit, SORT_ASC,
  $sorter_sejour_sortie, SORT_ASC,
  $sorter_sejour_entree, SORT_DESC,
  $listSejours
);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"       , $date);
$smarty->assign("praticien"  , $praticien);
$smarty->assign("listSejours", $listSejours);

$smarty->display("inc_vw_hospi.tpl");
