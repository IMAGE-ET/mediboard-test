<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();

$facture_id  = CValue::getOrSession("facture_id");
$patient_id         = CValue::getOrSession("patient_id");
$consult_id         = CValue::get("consult_id");
$facture_id         = CValue::get("facture_id");

$derconsult_id = null;
$facture = new CFactureConsult();
$consult = null;
$assurances_patient = array();

if ($consult_id) {
  $consult = new CConsultation();
  $consult->load($consult_id);
  $consult->loadRefsReglements();
  
  if ($facture->load($consult->facture_id)) {
    $facture->loadRefs();
    if (count($facture->_ref_consults) == 0) {
      $facture->tarif = null;
      $facture->delete();
      $facture = new CFactureConsult();
    }
    
    // Recalcul de la facture ?    
    else {
      $total_patient  = 0;
      $total_tiers    = 0;
      
      foreach ($facture->_ref_consults as $consultation) {
        $total_patient  += $consultation->du_patient;
        $total_tiers    += $consultation->du_tiers;
      }
      
      if ($total_patient != $facture->du_patient || $total_tiers != $facture->du_tiers) {
        $facture->du_patient = $total_patient;
        $facture->du_tiers   = $total_tiers  ;
        $facture->tarif = null;
        $facture->store();
      }
    }
  }
  elseif ($facture_id) {
    $facture->load($facture_id);
    $facture->loadRefs();
    if (count($facture->_ref_consults) == 0) {
      $facture->tarif = null;
      $facture->delete();
      $facture = new CFactureConsult();
    }
  }
}
elseif ($facture_id) {
  $facture->load($facture_id); 
  $facture->loadRefs();
  if ($facture->_ref_consults) {
    $last_consult = reset($facture->_ref_consults);
    $derconsult_id = $last_consult->_id;
  }
  $facture->_ref_patient->loadRefsCorrespondantsPatient();
}

$facture->loadRefsNotes();

$reglement   = new CReglement();

$banque      = new CBanque();
$banques     = $banque->loadList(null, "nom");

// Instanciation d'un acte tarmed pour l'ajout de ligne dans la facture
$acte_tarmed = null;
if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ) {
  $acte_tarmed = new CActeTarmed();
  $acte_tarmed->date     = mbDate();
  $acte_tarmed->quantite = 1;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture"       , $facture);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("acte_tarmed"   , $acte_tarmed);
$smarty->assign("derconsult_id" , $derconsult_id);
$smarty->assign("banques"       , $banques);
$smarty->assign("consult"       , $consult);

if (!CValue::get("not_load_banque")) {
  $smarty->assign("factures"    , array(new CFactureConsult()));
}

$smarty->assign("etat_ouvert"   , CValue::getOrSession("etat_ouvert", 1));
$smarty->assign("etat_cloture"  , CValue::getOrSession("etat_cloture", 1));
$smarty->assign("date"          , mbDate());
$smarty->assign("chirSel"       , CValue::getOrSession("chirSel", "-1"));

$smarty->display("inc_vw_facturation.tpl");
?>