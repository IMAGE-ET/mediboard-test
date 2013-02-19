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
$consult_id  = CValue::get("consult_id");
    
$derconsult_id = null;
$facture = new CFactureCabinet();
$consult = null;

if ($consult_id) {
  $consult = new CConsultation();
  $consult->load($consult_id);
  $consult->loadRefsReglements();
  
  if (!$facture->load($consult->facture_id) && $facture_id) {
    $facture->load($facture_id);
  }
  $facture->_consult_id = $consult_id;
}
elseif ($facture_id) {
  $facture->load($facture_id);
}

$facture->loadRefs();
$facture->loadRefsNotes();
if ($facture->_ref_consults) {
  $last_consult = $facture->_ref_last_consult;
  $derconsult_id = $facture->_ref_last_consult->_id;
}
$facture->_ref_patient->loadRefsCorrespondantsPatient();
$facture->loadRefsNotes();

$reglement   = new CReglement();
$banque      = new CBanque();
$banques     = $banque->loadList(null, "nom");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture"       , $facture);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("derconsult_id" , $derconsult_id);
$smarty->assign("banques"       , $banques);
$smarty->assign("consult"       , $consult);

if (!CValue::get("not_load_banque")) {
  $smarty->assign("factures"    , array(new CFactureCabinet()));
}

$smarty->assign("etat_ouvert"   , CValue::getOrSession("etat_ouvert", 1));
$smarty->assign("etat_cloture"  , CValue::getOrSession("etat_cloture", 1));
$smarty->assign("date"          , mbDate());
$smarty->assign("chirSel"       , CValue::getOrSession("chirSel", "-1"));

$smarty->display("inc_vw_facturation.tpl");
?>