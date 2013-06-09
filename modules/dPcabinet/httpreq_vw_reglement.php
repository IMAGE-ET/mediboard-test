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

// Utilisateur sélectionné ou utilisateur courant
$prat_id      = CValue::getOrSession("chirSel", 0);
$selConsult   = CValue::getOrSession("selConsult", null);

// Chargement des banques
$orderBanque = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null,$orderBanque);

$consult = new CConsultation();

// Test compliqué afin de savoir quelle consultation charger
if (isset($_GET["selConsult"])) {
  if ($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    CValue::setSession("chirSel", $prat_id);
  }
  else {
    $consult = new CConsultation();
    $selConsult = null;
    CValue::setSession("selConsult");
  }
}
else {
  if ($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    if ($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      CValue::setSession("selConsult");
    }
  }
}

$userSel = CMediusers::get($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// Vérification des droits sur les praticiens
$listChir = CConsultation::loadPraticiens(PERM_EDIT);

if (!$userSel->isMedical()) {
  CAppUI::setMsg("Vous devez selectionner un personnel de santé", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

// Consultation courante
$consult->_ref_chir = $userSel;
if ($selConsult) {
  $consult->load($selConsult);
  
  CCanDo::checkObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  // Some Forward references
  $consult->loadRefsFwd();
  $consult->loadRefConsultAnesth();
    
  // Patient
  $patient =& $consult->_ref_patient;
}

if (CModule::getActive("fse")) {
  $fse = CFseFactory::createFSE();
  if ($fse) {
    $fse->loadIdsFSE($consult);
    $fse->makeFSE($consult);
    CFseFactory::createCPS()->loadIdCPS($consult->_ref_chir);
    CFseFactory::createCV()->loadIdVitale($consult->_ref_patient);
  }  
}

$consult->loadRefs();  

// Récupération des tarifs
$tarif = new CTarif;
$tarifs = array();
if (!$consult->tarif || $consult->tarif == "pursue") {
  $order = "description";
  $where = array();
  $where["chir_id"] = "= '$userSel->user_id'";
  $tarifs["user"] = $tarif->loadList($where, $order);
  foreach ($tarifs["user"] as $_tarif) {
    $_tarif->getPrecodeReady();
  }
  
  $where = array();
  $where["function_id"] = "= '$userSel->function_id'";
  $tarifs["func"] = $tarif->loadList($where, $order);
  foreach ($tarifs["func"] as $_tarif) {
    $_tarif->getPrecodeReady();
  }
  if (CAppui::conf("dPcabinet Tarifs show_tarifs_etab"))  {
    $where = array();
    $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
    $tarifs["group"] = $tarif->loadList($where, $order);
    foreach ($tarifs["group"] as $_tarif) {
      $_tarif->getPrecodeReady();
    }
  }
}

// Règlements
$consult->loadRefsReglements();

// Reglement vide pour le formulaire
$reglement = new CReglement();
$reglement->consultation_id = $consult->_id;
$reglement->montant = round($consult->_du_restant_patient, 2);

// Codes et actes
$consult->loadRefsActes();

//Recherche de la facture pour cette consultation
$facture = $consult->loadRefFacture();

//Si on a pas de facture on recherche d'une facture ouverte 
if (!$facture->_id && CAppUI::conf("ref_pays") == 2) {
  $where    = array();
  $where["patient_id"] = "= '$consult->patient_id'";
  $plage = $consult->_ref_plageconsult;
  $praticien_id = $plage->pour_compte_id ? $plage->pour_compte_id : $prat_id;
  $where["praticien_id"] = "= '$praticien_id'";
  $where["cloture"] = " IS NULL";
  $facture->loadObject($where);
}

if ($facture->_id) {
  $facture->loadRefPatient();
  $facture->_ref_patient->loadRefsCorrespondantsPatient();
  $facture->loadRefPraticien();
  $facture->loadRefAssurance();
  $facture->loadRefsObjects();
  $facture->loadRefsReglements();
  $facture->loadRefsNotes();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("banques"  , $banques);
$smarty->assign("facture"  , $facture);
$smarty->assign("consult"  , $consult);
$smarty->assign("reglement", $reglement);
$smarty->assign("tarifs"   , $tarifs);
$smarty->assign("date"     , CMbDT::date());

$smarty->display("inc_vw_reglement.tpl");

?>