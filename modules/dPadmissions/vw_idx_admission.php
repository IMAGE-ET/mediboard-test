<?php

/**
 * $Id$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Filtres d'affichage

$selAdmis   = CValue::getOrSession("selAdmis", "0");
$selSaisis  = CValue::getOrSession("selSaisis", "0");
$order_way  = CValue::getOrSession("order_way", "ASC");
$order_col  = CValue::getOrSession("order_col", "patient_id");
$date       = CValue::getOrSession("date", CMbDT::date());
$type       = CValue::getOrSession("type");
$service_id = CValue::getOrSession("service_id");
$secteur_id = CValue::getOrSession("secteur_id");
$prat_id    = CValue::getOrSession("prat_id");
$period     = CValue::getOrSession("period");
$filterFunction = CValue::getOrSession("filterFunction");

$date_actuelle = CMbDT::dateTime("00:00:00");
$date_demain = CMbDT::dateTime("00:00:00", "+ 1 day");
$hier = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

// Récupération de la liste des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
if ($secteur_id) {
  $where["secteur_id"] = "= '$secteur_id'";
}
$service = new CService();
if ($service_id) {
  $service->load($service_id);
  $secteur_id = $service->secteur_id;
}
$services = $service->loadGroupList($where);

// Récupération de la liste des secteurs
$secteur = new CSecteur();
$secteurs = $secteur->loadList(null, "nom");

// Récupération de la liste des praticiens
$prat = CMediusers::get();
$prats = $prat->loadPraticiens();

$sejour = new CSejour();
$sejour->_type_admission = $type;
$sejour->service_id      = explode(",", $service_id);
$sejour->praticien_id    = $prat_id;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"       , $sejour);
$smarty->assign("date_demain"  , $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"         , $date);
$smarty->assign("selAdmis"     , $selAdmis);
$smarty->assign("selSaisis"    , $selSaisis);
$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("services"     , $services);
$smarty->assign("secteur_id"   , $secteur_id);
$smarty->assign("service_id"   , $service_id);
$smarty->assign("secteurs"     , $secteurs);
$smarty->assign("prats"        , $prats);
$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);
$smarty->assign("period"       , $period);
$smarty->assign("filterFunction", $filterFunction);

$smarty->display("vw_idx_admission.tpl");
