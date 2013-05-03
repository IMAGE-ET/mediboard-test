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
$facture_class  = CValue::get("facture_class");
$facture_id     = CValue::get("facture_id");
$relance_id     = CValue::get("relance_id");
$prat_id        = CValue::get("prat_id");
$date_min       = CValue::get("_date_min", CMbDT::date());
$date_max       = CValue::get("_date_max", CMbDT::date());
$type_relance   = CValue::get("type_relance");
$type_pdf       = CValue::get("type_pdf", "bvr");

$factures = array();
$facture = new $facture_class;
//si on a une facture_id on la charge
if ($facture_id) {
  $factures[$facture_id] = $facture->load($facture_id);
}
else {
  $where = array();
  $where["praticien_id"] = " = '$prat_id'";
  $where["cloture"]      = "BETWEEN '$date_min' AND '$date_max'";
  $factures = $facture->loadList($where, "facture_id DESC", null, "patient_id");
}

$facture_pdf = new CEditPdf();
$facture_pdf->factures = $factures;

if ($type_pdf == "bvr") {
  $facture_pdf->editFactureBVR();
}

if ($type_pdf == "bvr_TS") {
  $facture_pdf->editFactureBVR("TS");
}

if ($type_pdf == "justificatif") {
  $facture_pdf->editJustificatif();
}

if ($type_pdf == "relance") {
  $relance = new CRelance();
  $relance->load($relance_id);
  $facture_pdf->factures = $factures;
  $facture_pdf->relance = $relance;
  $facture_pdf->editRelance();
}

?>