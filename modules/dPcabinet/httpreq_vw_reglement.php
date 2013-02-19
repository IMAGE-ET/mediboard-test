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
if (CAppUI::pref("pratOnlyForConsult", 1)) {
  $listChir = $userSel->loadPraticiens(PERM_EDIT);
}
else {
  $listChir = $userSel->loadProfessionnelDeSante(PERM_EDIT);
}

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
}

// Règlements
$consult->loadRefsReglements();

// Reglement vide pour le formulaire
$reglement = new CReglement();
$reglement->consultation_id = $consult->_id;
$reglement->montant = round($consult->_du_restant_patient, 2);

// Codes et actes
$consult->loadRefsActes();

$facture_patient = new CFactureCabinet();
$facture         = new CFactureCabinet();
$where = array();
$where["patient_id"] = "= '$consult->patient_id'";
if ($consult->_ref_plageconsult->pour_compte_id) {
  $where["praticien_id"] = "= '".$consult->_ref_plageconsult->pour_compte_id."'";
}
else {
  $where["praticien_id"] = "= '$prat_id'";
}

if (CAppUI::conf("dPfacturation CFactureCabinet use_create_bill")) {
  $liaison = new CFactureLiaison();
  $liaison->object_id     = $consult->_id;
  $liaison->object_class  = $consult->_class;
  $liaison->facture_class = "CFactureCabinet";
  if ($liaison->loadMatchingObject()) {
    $facture_patient = $liaison->loadRefFacture();
    $facture_patient->loadRefs();
  }
}
elseif (CAppUI::conf("ref_pays") == 1 && $consult->facture_id) {
  if ($facture->load($consult->facture_id)) {
    $facture_patient = $facture;
    $facture_patient->loadRefs();
  }
}
elseif (CAppUI::conf("ref_pays") == 2) {
  $where["cloture"] = " IS NULL";
  //On essaie de retrouver une facture ouverte
  if ($facture->loadObject($where)) {
    $facture_patient = $facture;
    $facture_patient->loadRefs();
  }
  else {
    $where["cloture"] = " IS NOT NULL";
    if ($factures = $facture->loadList($where)) {
      foreach ($factures as $_facture) {
        $_facture->loadRefPatient();
        $_facture->loadRefsConsultation();
        foreach ($_facture->_ref_consults as $consultation) {
          if ($consultation->_id == $consult->_id) {
            $facture_patient = $_facture;
            $facture_patient->loadRefs();
            break;
          }
        }
      }  
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("banques"  , $banques);
$smarty->assign("facture"  , $facture_patient);
$smarty->assign("consult"  , $consult);
$smarty->assign("reglement", $reglement);
$smarty->assign("tarifs"   , $tarifs);
$smarty->assign("date"     , mbDate());

$smarty->display("inc_vw_reglement.tpl");

?>