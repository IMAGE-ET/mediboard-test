<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */
CCanDo::checkEdit();
$facture_class  = CValue::get("facture_class", "CFactureCabinet");
$facture_id     = CValue::get("facture_id");
$prat_id        = CValue::get("prat_id");
$definitive     = CValue::get("definitive");
$tri            = CValue::get("tri");
$type_fact      = CValue::get("type_fact");
$date_min       = CValue::get("_date_min", CMbDT::date());
$date_max       = CValue::get("_date_max", CMbDT::date());

$factures = array();
/* @var CFacture $facture*/
$facture = new $facture_class;

$where = array();
$ljoin = array();
if ($prat_id) {
  $where["praticien_id"] = " = '$prat_id'";
}
$where["cloture"]      = "BETWEEN '$date_min' AND '$date_max'";
if ($facture_id) {
  $where["facture_id"] = "= '$facture_id'";
}
$order = "facture_id";
if ($tri == "nom_patient") {
  $ljoin["patients"] = "patients.patient_id = ".$facture->_spec->table.".patient_id";
  $order = "patients.nom";
}
if ($type_fact == "patient") {
  $where[] = "assurance_maladie IS NULL AND assurance_accident IS NULL";
}
if ($type_fact == "garant") {
  $where[] = "assurance_maladie IS NOT NULL OR assurance_accident IS NOT NULL";
}
$factures = $facture->loadList($where, $order, null, "patient_id", $ljoin);

foreach ($factures as $_facture) {
  /* @var CFacture $_facture*/
  $_facture->loadRefPatient();
  $_facture->loadRefPraticien();
  $_facture->loadRefAssurance();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"    , $factures);
$smarty->assign("definitive"  , $definitive);
$smarty->assign("facture"     , $facture);

$smarty->display("inc_print_bill.tpl");