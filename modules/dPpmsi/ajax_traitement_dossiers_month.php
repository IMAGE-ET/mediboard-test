<?php 

/**
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();
$ds = CSQLDataSource::get("std");

$filterFunction = CValue::get("filterFunction");
$type           = CValue::get("_type_admission");
$service_id     = CValue::get("service_id");
$service_id     = explode(",", $service_id);

CMbArray::removeValue("", $service_id);
$prat_id        = CValue::get("prat_id");
$order_way      = CValue::get("order_way", "ASC");
$order_col      = CValue::get("order_col", "patient_id");
$tri_recept     = CValue::get("tri_recept");
$tri_complet    = CValue::get("tri_complet");
$date           = CValue::getOrSession("date", CMbDT::date());

$month_min  = CMbDT::date("first day of +0 month", $date);
$lastmonth  = CMbDT::date("last day of -1 month" , $date);
$nextmonth  = CMbDT::date("first day of +1 month", $date);
$bank_holidays = CMbDate::getHolidays($date);

$group = CGroups::loadCurrent();
$where = array();
$leftjoin = array();
// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day < $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day] = array(
    "sortie" => "0",
    "traitement" => "0",
    "complet" => "0",
  );
}

// filtre sur les types d'admission
if ($type == "ambucomp") {
  $where["sejour.type"] = "= 'ambu' OR `sejour`.`type` = 'comp')";
}
elseif ($type) {
  $where["sejour.type"] = " = '$type'";
}
else {
  $where["sejour.type"] = "!= 'urg' AND `sejour`.`type` != 'seances'";
}

// filtre sur les services
if (count($service_id)) {
  $leftjoin["affectation"] = " ON affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie";
  $leftjoin["lit"] = " ON affectation.lit_id = lit.lit_id";
  $leftjoin["chambre"] = " ON lit.chambre_id = chambre.chambre_id";
  $leftjoin["service"] = " ON chambre.service_id = service.service_id";

  $where["sejour.service_id"] = $ds->prepareIn($service_id)." OR affectation.service_id ". $ds->prepareIn($service_id);
}

// filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = " = '$prat_id'";
}

$month_min  = CMbDT::dateTime(null, $month_min);
$nextmonth  = CMbDT::dateTime(null, $nextmonth);

// Liste des sorties par jour
$request = new CRequest();
$request->addSelect(array("DATE_FORMAT(sejour.sortie_reelle, '%Y-%m-%d') AS 'date'", "COUNT(sejour.sejour_id) AS 'num'"));
$request->addTable("sejour");
$where["sejour.sortie_reelle"] = "BETWEEN '$month_min' AND '$nextmonth'";
$where["sejour.group_id"] = " = '$group->_id'";
$where["sejour.annule"] = " = '0'";
$request->addWhere($where);
$request->addLJoin($leftjoin);
$request->addGroup("date");
$request->addOrder("date");

foreach ($ds->loadHashList($request->makeSelect()) as $day => $_sortie) {
  $days[$day]["sortie"] = $_sortie;
}

// Liste des sorties dont le dossier n'a pas été reçu
$leftjoin["traitement_dossier"] = "traitement_dossier.sejour_id = sejour.sejour_id";
$where["traitement_dossier.traitement"] = " IS NOT NULL";
$request->addWhere($where);
$request->addLJoin($leftjoin);
foreach ($ds->loadHashList($request->makeSelect()) as $day => $_traitement) {
  $days[$day]["traitement"] = $_traitement;
}

// Liste des sorties dont le dossier est traité
unset($where['traitement_dossier.traitement']);
$request->where = array();
$where["traitement_dossier.validate"] = " IS NOT NULL";
$request->addWhere($where);
foreach ($ds->loadHashList($request->makeSelect()) as $day => $_complet) {
  $days[$day]["complet"] = $_complet;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filterFunction", $filterFunction);
$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("tri_recept"   , $tri_recept);
$smarty->assign("tri_complet"  , $tri_complet);
$smarty->assign('date'         , $date);
$smarty->assign('lastmonth'    , $lastmonth);
$smarty->assign('nextmonth'    , $nextmonth);
$smarty->assign('bank_holidays', $bank_holidays);
$smarty->assign('days'         , $days);

$smarty->display("traitement_dossiers/inc_traitement_dossiers_month.tpl");