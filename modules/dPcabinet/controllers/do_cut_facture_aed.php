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

$factureconsult_id  = CValue::post("factureconsult_id");
$facture = new CFactureConsult();
$facture->load($factureconsult_id);
$facture->loadRefs();

$nb_factures = array();
$proposition_tarifs = array();
$nbmax = 1;
$total_factures = 0;
foreach ($facture->_montant_factures_caisse as $caisse => $montant) {
  $nb_factures[$caisse] = CValue::post("nb_factures_$caisse");
  
  if (CValue::post("nb_factures_$caisse") !=1 ) {
    $nbmax = CValue::post("nb_factures_$caisse");
  }
  
  for ($i=0; $i<$nb_factures[$caisse]; $i++) {
    $proposition_tarifs[] = CValue::post("tarif$caisse"."_$i");
  }
  $total_factures += $montant;
}

if ($nbmax > 1) {
  $montants = 0;
  foreach ($proposition_tarifs as $key=>$tarif) {
    if ($key!=0) {
      $facture_sup = new CFactureConsult();
      $facture_sup->type_facture  = $facture->type_facture;
      $facture_sup->praticien_id  = $facture->praticien_id;
      $facture_sup->patient_id    = $facture->patient_id;
      $facture_sup->ouverture     = $facture->ouverture;
      $facture_sup->cloture       = $facture->cloture;
      $facture_sup->du_patient    = $tarif;
      $montants += $tarif;
      if ($msg = $facture_sup->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
  }
  
  $montants += $proposition_tarifs[0];
  $facture->remise = $total_factures - $montants;
  $facture->du_patient = $proposition_tarifs[0] + $facture->remise;
  $facture->du_tiers   = 0;
  $facture->tarif   = null;
  if ($msg = $facture->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}

echo CAppUI::getMsg();
CApp::rip();
?>