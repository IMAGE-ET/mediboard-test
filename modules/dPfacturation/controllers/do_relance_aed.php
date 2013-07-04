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
$_date_min      = CValue::post("_date_min");
$_date_max      = CValue::post("_date_max");
$type_relance   = CValue::post("type_relance", 0);
$facture_class  = CValue::post("facture_class");
$chirSel        = CValue::post("chir");

if ($_date_min) {
  $where = array();
  if ($facture_class == "CFactureEtablissement") {
    $where["temporaire"] = " = '0'";
  }
  if (($chirSel && $facture_class == "CFactureEtablissement") || $facture_class == "CFactureCabinet") {
    $where["praticien_id"] =" = '$chirSel' ";
  }
  $where["cloture"] = "<= '$_date_max'";
  
  $facture  = new $facture_class;
  $factures = $facture->loadList($where);
  
  foreach ($factures as $key => $_facture) {
    $_facture->loadRefsObjects();
    $_facture->loadRefsReglements();
    $_facture->loadRefsRelances();
    if (!$_facture->_is_relancable || count($_facture->_ref_relances)+1 < $type_relance) {
      unset($factures[$key]);
    }
  }

  if (count($factures)) {
    $facture_pdf = new CEditPdf();
    $facture_pdf->pdf = new CMbPdf('P', 'mm');
    $facture_pdf->pdf->setPrintHeader(false);
    $facture_pdf->pdf->setPrintFooter(false);
    $facture_pdf->font = "vera";
    $facture_pdf->fontb = $facture_pdf->font."b";

    foreach ($factures as $_facture) {
      $relance = new CRelance();
      $relance->object_id    = $_facture->_id;
      $relance->object_class = $_facture->_class;
      if ($msg = $relance->store()) {
        return $msg;
      }

      $facture_pdf->facture = $_facture;
      $facture_pdf->patient = $facture_pdf->facture->loadRefPatient();
      $facture_pdf->facture->_ref_patient->loadRefsCorrespondantsPatient();
      $facture_pdf->praticien = $facture_pdf->facture->loadRefPraticien();
      $facture_pdf->facture->loadRefAssurance();
      $facture_pdf->function_prat = $facture_pdf->praticien->loadRefFunction();
      $facture_pdf->group = $facture_pdf->function_prat->loadRefGroup();
      $facture_pdf->adherent = $facture_pdf->praticien->adherent;

      $facture_pdf->relance = $relance;
      $facture_pdf->editRelanceEntete();
      $facture_pdf->editBVR($facture_pdf->relance->_montant);
    }
    $facture_pdf->pdf->Output('Relances.pdf', "I");
  }
  /*
  CAppUI::setMsg(count($factures)." relance(s) crée(s)", UI_MSG_OK);
  echo CAppUI::getMsg();
  CApp::rip();
  */
}
else {
  $do = new CDoObjectAddEdit("CRelance");
  $do->doIt();
}
