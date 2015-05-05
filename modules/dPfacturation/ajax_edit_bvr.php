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
$factures       = CValue::get("factures", array());
$definitive     = CValue::get("definitive", 0);
$tiers_soldant  = CValue::get("tiers_soldant", 0);

//impression
$factures_id = array();
foreach ($factures as $value) {
  $factures_id[$value] = $value;
}

if ($type_pdf == "relance" && !$facture_class) {
  $relance = new CRelance();
  $relance->load($relance_id);
  $facture_class = $relance->object_class;
}

$factures = array();
$facture = new $facture_class;
//si on a une facture_id on la charge
if ($facture_id) {
  $factures[$facture_id] = $facture->load($facture_id);
}
elseif (count($factures_id)) {
  $where = array();
  $where["facture_id"] = CSQLDataSource::prepareIn(array_keys($factures_id));
  $factures = $facture->loadList($where);
}
else {
  $where = array();
  $where["praticien_id"] = " = '$prat_id'";
  $where["cloture"]      = "BETWEEN '$date_min' AND '$date_max'";
  $factures = $facture->loadList($where, "facture_id DESC", null, "facture_id");
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

if ($type_pdf == "bvr_justif") {
  $facture_pdf->editFactureBVRJustif();
}
if ($type_pdf == "impression") {
  $facture_pdf->printBill($tiers_soldant);

  $journal_pdf = new CEditJournal();
  $journal_pdf->type_pdf = "debiteur";
  $journal_pdf->factures = $factures;
  foreach ($journal_pdf->factures as $fact) {
    /** @var CFacture $fact */
    $fact->loadRefsObjects();
    $fact->loadRefPatient();
    $fact->loadRefPraticien();
    $fact->loadRefsReglements();
    $fact->isRelancable();
  }
  $journal_pdf->editJournal(false);

  $journal_pdf->type_pdf = "checklist";
  $journal_pdf->definitive = $definitive;
  $journal_pdf->editJournal(false);

  if (!$facture_id) {
    if ($definitive) {
      foreach ($factures as $_facture) {
        if (!$_facture->definitive) {
          $_facture->definitive = 1;
          if ($msg = $_facture->store()) {
            mbLog($msg);
          }
        }
      }
    }
    unset($_GET["suppressHeaders"]);
  }
}

if ($type_pdf == "relance") {
  $relance = new CRelance();
  $relance->load($relance_id);
  if ($relance->_id) {
    $facture_pdf->factures = array($relance->loadRefFacture());
  }
  $facture_pdf->relance = $relance;
  $facture_pdf->editRelance();
}