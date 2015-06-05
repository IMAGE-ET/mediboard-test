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

$selSortis       = CValue::getOrSession("selSortis", "0");
$order_col       = CValue::getOrSession("order_col", "patient_id");
$order_way       = CValue::getOrSession("order_way", "ASC");
$date            = CValue::getOrSession("date", CMbDT::date());
$type            = CValue::getOrSession("type");
$services_ids    = CValue::getOrSession("services_ids");
$prat_id         = CValue::getOrSession("prat_id");
$only_confirmed  = CValue::getOrSession("only_confirmed");
$period          = CValue::getOrSession("period");
$filterFunction  = CValue::getOrSession("filterFunction");
$enabled_service = CValue::getOrSession("active_filter_services", 0);
$date_actuelle   = CMbDT::dateTime("00:00:00");
$date_demain     = CMbDT::dateTime("00:00:00", "+ 1 day");
$hier            = CMbDT::date("- 1 day", $date);
$demain          = CMbDT::date("+ 1 day", $date);

$services_ids = CService::getServicesIdsPref($services_ids);

// Récupération de la liste des praticiens
$prat = CMediusers::get();
$prats = $prat->loadPraticiens();

$sejour = new CSejour();
$sejour->_type_admission = $type;
$sejour->praticien_id    = $prat_id;

// Liste des types d'admission possibles
$list_type_admission = $sejour->_specs["_type_admission"]->_list;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"          , $sejour);
$smarty->assign("date_demain"     , $date_demain);
$smarty->assign("date_actuelle"   , $date_actuelle);
$smarty->assign("date"            , $date);
$smarty->assign("selSortis"       , $selSortis);
$smarty->assign("order_way"       , $order_way);
$smarty->assign("order_col"       , $order_col);
$smarty->assign("prats"           , $prats);
$smarty->assign("hier"            , $hier);
$smarty->assign("demain"          , $demain);
$smarty->assign("period"          , $period);
$smarty->assign("only_confirmed"  , $only_confirmed);
$smarty->assign("filterFunction"  , $filterFunction);
$smarty->assign("list_type_ad"    , $list_type_admission);
$smarty->assign('enabled_service' , $enabled_service);

$smarty->display("vw_idx_sortie.tpl");
