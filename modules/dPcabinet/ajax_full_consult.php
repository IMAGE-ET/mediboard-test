<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$consult_id = CValue::get("consult_id");
$sejour_id  = CValue::get("sejour_id");

$consult = new CConsultation;
$consult->load($consult_id);

if (!$consult->_id) {
  CAppUI::stepAjax(CAppUI::tr("CConsultation.none"));
  CApp::rip();
}

$consult->canEdit();

$patient = $consult->loadRefPatient();
$patient->loadRefPhotoIdentite();
$patient->loadRefsCorrespondants();
$dossier_medical = $patient->loadRefDossierMedical();
$consult_anesth = $consult->loadRefConsultAnesth();

$list_etat_dents = array();

if ($dossier_medical->_id) {
  $etat_dents = $dossier_medical->loadRefsEtatsDents();
  foreach ($etat_dents as $etat) {
    $list_etat_dents[$etat->dent] = $etat->etat;
  }
}


// Initialisation d'un acte NGAP
$acte_ngap = new CActeNGAP();
$acte_ngap->quantite    = 1;
$acte_ngap->coefficient = 1;
$acte_ngap->loadListExecutants();

// Si le module Tarmed est installé chargement d'un acte
$acte_tarmed = null;
$acte_caisse = null;
if (CModule::getActive("tarmed")) {
  // Initialisation d'un acte Tarmed
  $acte_tarmed = new CActeTarmed();
  $acte_tarmed->quantite = 1;
  $acte_tarmed->loadListExecutants();
  $acte_tarmed->loadRefExecutant();
  $acte_caisse = new CActeCaisse();
  $acte_caisse->quantite = 1;
  $acte_caisse->loadListExecutants();
  $acte_caisse->loadRefExecutant();
  $acte_caisse->loadListCaisses();
}

// Tableau de contraintes pour les champs du RPU
// Contraintes sur le mode d'entree / provenance
//$contrainteProvenance[6] = array("", 1, 2, 3, 4);
$contrainteProvenance[7] = array("", 1, 2, 3, 4);
$contrainteProvenance[8] = array("", 5, 8);

// Contraintes sur le mode de sortie / destination
$contrainteDestination["mutation" ] = array("", 1, 2, 3, 4);
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"   ] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["mutation" ] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"   ] = array("", "FUGUE", "SCAM", "PSA", "REO");

$list_etat_dents = array();
if ($consult->_id) {
  $dossier_medical = $consult->_ref_patient->_ref_dossier_medical;
  if ($dossier_medical->_id) {
    $etat_dents = $dossier_medical->loadRefsEtatsDents();
    foreach ($etat_dents as $etat) {
      $list_etat_dents[$etat->dent] = $etat->etat;
    }
  }
}

$consult->loadRefsActesTarmed();
$consult->loadRefsActesCaisse();
$soustotal_base = array("tarmed" => 0, "caisse" => 0);
$soustotal_dh   = array("tarmed" => 0, "caisse" => 0);
if ($consult->_ref_actes_tarmed) {
  foreach($consult->_ref_actes_tarmed as $acte){
    $soustotal_base["tarmed"] += $acte->montant_base;
    $soustotal_dh["tarmed"]   += $acte->montant_depassement; 
  }
}
if ($consult->_ref_actes_caisse) {
  foreach($consult->_ref_actes_caisse as $acte){
    $soustotal_base["caisse"] += $acte->montant_base;
    $soustotal_dh["caisse"]   += $acte->montant_depassement; 
  }
}
$total["tarmed"] = $soustotal_base["tarmed"] + $soustotal_dh["tarmed"];
$total["caisse"] = $soustotal_base["caisse"] + $soustotal_dh["caisse"];
$total["tarmed"] = round($total["tarmed"],2);
$total["caisse"] = round($total["caisse"],2);

if (CModule::getActive("maternite")) {
  $consult->loadRefGrossesse();
}

$user = CMediusers::get();
$user->isAnesth();
$user->isPraticien();

$smarty = new CSmartyDP;

$smarty->assign("consult"        , $consult);
$smarty->assign("consult_anesth" , $consult_anesth);
$smarty->assign("patient"        , $patient);
$smarty->assign("_is_anesth"     , $user->isAnesth());
$smarty->assign("antecedent"     , new CAntecedent);
$smarty->assign("traitement"     , new CTraitement);
$smarty->assign("acte_ngap"      , $acte_ngap);
$smarty->assign("acte_tarmed"    , $acte_tarmed);
$smarty->assign("acte_caisse"    , $acte_caisse);
$smarty->assign("total"          , $total);
if(CModule::getActive("dPprescription")){
  $smarty->assign("line"           , new CPrescriptionLineMedicament);
}
$smarty->assign("userSel"        , $user);
$smarty->assign("sejour_id"      , $sejour_id);
$smarty->assign("today"          , mbDate());
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));

if ($consult_anesth->_id) {
  $consult_anesth->loadRefOperation();
  $consult_anesth->loadRefsTechniques();
  $anesth = new CTypeAnesth;
  $orderanesth = "name";
  $anesth = $anesth->loadList(null,$orderanesth);
  
  $smarty->assign("list_etat_dents", $list_etat_dents);
  $smarty->assign("mins"           , range(0, 15-1, 1));
  $smarty->assign("secs"           , range(0, 60-1, 1));
  $smarty->assign("examComp"       , new CExamComp);
  $smarty->assign("techniquesComp" , new CTechniqueComp);
  $smarty->assign("anesth"         , $anesth);
  $smarty->assign("view_prescription", 0);
  
  if (CAppUI::conf("dPcabinet CConsultAnesth show_facteurs_risque")) {
    $sejour = new CSejour;
    $sejour->load($sejour_id);
    $sejour->loadRefDossierMedical();
    $smarty->assign("sejour"       , $sejour);
  }
  
  if ($consult_anesth->operation_id) {
    $listAnesths = new CMediusers;
    $listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);
    $smarty->assign("listAnesths", $listAnesths);
  }
}

$smarty->display("inc_full_consult.tpl");

?>