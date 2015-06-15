<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
CCanDo::checkEdit();
$facture_class  = CValue::get("facture_class", "CFactureCabinet");
$facture_id     = CValue::get("facture_id");
$prat_id        = CValue::get("prat_id");
$definitive     = CValue::get("definitive");
$tri            = CValue::get("tri");
$type_fact      = CValue::get("type_fact");
$tiers_soldant  = CValue::get("tiers_soldant", 0);
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
$where["cloture"] = "BETWEEN '$date_min' AND '$date_max'";
$where["definitive"] = "= '0'";

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

$montant_total = 0;
foreach ($factures as $_facture) {
  /* @var CFacture $_facture*/
  if ($tiers_soldant) {
    $_facture->loadRefAssurance();
    if (!(($_facture->assurance_maladie && $_facture->_ref_assurance_maladie->type_pec == "TS") ||
        ($_facture->assurance_accident && $_facture->_ref_assurance_accident->type_pec == "TS"))) {
      if (count($_facture->loadRefsReglements())) {
        unset($factures[$_facture->_id]);
      }
    }
  }
}
foreach ($factures as $_facture) {
  /* @var CFacture $_facture*/
  $_facture->loadRefPatient();
  $_facture->loadRefPraticien();
  $_facture->loadRefsObjects();
  if (!count($_facture->_ref_consults) && !count($_facture->_ref_sejours)) {
    unset($factures[$_facture->_id]);
  }
  else {
    $_facture->loadRefsReglements();
    $montant_total += $_facture->_montant_avec_remise;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"      , $factures);
$smarty->assign("tiers_soldant" , $tiers_soldant);
$smarty->assign("definitive"    , $definitive);
$smarty->assign("facture"       , $facture);
$smarty->assign("montant_total" , $montant_total);
$smarty->assign("uniq_checklist", CValue::get("uniq_checklist"));
$smarty->assign("facture_class" , $facture_class);

$smarty->display("inc_print_bill.tpl");