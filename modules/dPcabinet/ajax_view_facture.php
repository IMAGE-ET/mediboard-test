<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
$facture_id   = CValue::getOrSession("facture_id");
$consult_id   = CValue::get("consult_id");
$object_class = CValue::getOrSession("object_class", "CFactureCabinet");
$object_class = $object_class == "CConsultation" ? "CFactureCabinet" : $object_class;

$derconsult_id = null;
$consult = null;
$facture = new $object_class;
$assurances_patient = array();

if ($consult_id) {
  $consult = new CConsultation();
  $consult->load($consult_id);
  $facture = $consult->loadRefFacture();
}
elseif ($facture_id) {
  $facture->load($facture_id);
}

$facture->loadRefPatient();
$facture->_ref_patient->loadRefsCorrespondantsPatient();
$facture->loadRefPraticien();
$facture->loadRefAssurance();
$facture->loadRefsObjects();
$facture->loadRefsReglements();
$facture->loadRefsRelances();
$facture->loadRefsNotes();
if ($facture->_ref_consults) {
  $derconsult_id = $facture->_ref_last_consult->_id;
}

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
  $smarty->assign("factures"    , array(new $object_class()));
}

$smarty->assign("etat_ouvert"   , CValue::getOrSession("etat_ouvert", 1));
$smarty->assign("etat_cloture"  , CValue::getOrSession("etat_cloture", 1));
$smarty->assign("date"          , CMbDT::date());
$smarty->assign("chirSel"       , CValue::getOrSession("chirSel", "-1"));

$smarty->display("inc_vw_facturation.tpl");
