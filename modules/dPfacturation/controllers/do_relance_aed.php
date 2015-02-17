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
  /* @var CFactureCabinet $facture*/
  $facture  = new $facture_class;

  $where = array();
  if ($facture_class == "CFactureEtablissement") {
    $where["temporaire"] = " = '0'";
  }
  if (($chirSel && $facture_class == "CFactureEtablissement") || $facture_class == "CFactureCabinet") {
    $where["praticien_id"] =" = '$chirSel' ";
  }
  $where[] = "(du_patient <> '0' AND patient_date_reglement IS NULL)
            || (du_tiers <> '0' AND tiers_date_reglement IS NULL)";
  $where[] = "cloture IS NOT NULL AND cloture <= '$_date_max'";
  $where[] = "NOT EXISTS (
    SELECT * FROM `facture_relance`
    WHERE `facture_relance`.`object_id` = ".$facture->_spec->table.".`facture_id`
    AND facture_relance.object_class = '$facture_class'
    AND facture_relance.numero >= '$type_relance'
  )";

  $factures_ids = $facture->loadIds($where, "cloture DESC", null, "facture_id");

  $factures = array();
  foreach ($factures_ids as $facture_id) {
    /* @var CFacture $_facture*/
    $_facture = new $facture_class;
    $_facture->load($facture_id);
    $_facture->loadRefPatient();
    $_facture->loadRefsObjects();
    $_facture->loadRefsReglements();
    $_facture->loadRefsRelances();
    $_facture->isRelancable();
    $not_exist_objets = !count($_facture->_ref_consults) && !count($_facture->_ref_sejours);
    if (!$_facture->_is_relancable || count($_facture->_ref_relances)+1 < $type_relance || $not_exist_objets) {
      //mbTrace("echec");
    }
    else {
      $factures[$facture_id] = $_facture;
    }
  }

  if (count($factures)) {
    $nb_generate_pdf_relance = CAppUI::conf("dPfacturation CRelance nb_generate_pdf_relance");
    $facture_pdf = new CEditPdf();
    $facture_pdf->pdf = new CMbPdf('P', 'mm');
    $facture_pdf->pdf->setPrintHeader(false);
    $facture_pdf->pdf->setPrintFooter(false);
    $facture_pdf->font = "vera";
    $facture_pdf->fontb = $facture_pdf->font."b";
    $nb_fact = 0;
    foreach ($factures as $_facture) {
      $nb_fact++;
      if ($nb_fact > $nb_generate_pdf_relance) {
        continue;
      }
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
