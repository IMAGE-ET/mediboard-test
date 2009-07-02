<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPlanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $tab, $dPconfig, $g;

$can->needsRead();

$sejour_id    = mbGetValueFromGetOrSession("sejour_id");
$patient_id   = mbGetValueFromGet("patient_id");
$praticien_id = mbGetValueFromGet("praticien_id");

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

// L'utilisateur est-il un praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien() and !$praticien_id) {
  $praticien_id = $mediuser->user_id;
}

// Chargement du praticien
$praticien = new CMediusers;
if ($praticien_id) {
  $praticien->load($praticien_id);
}

// Chargement du patient
$patient = new CPatient;
if ($patient_id) {
  $patient->load($patient_id);
}

// Vérification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);
$categorie_prat = array();
foreach($listPraticiens as $keyPrat =>$prat){
  $prat->loadRefsFwd();
  $categorie_prat[$keyPrat] = $prat->_ref_discipline->categorie;
}

// On récupére le séjour
$sejour = new CSejour;
if ($sejour_id) {
  $sejour->load($sejour_id);
  $sejour->loadRefs();
  
  // On vérifie que l'utilisateur a les droits sur le sejour
  if (!$sejour->canRead()) {
    $AppUI->setMsg("Vous n'avez pas accés à ce séjour", UI_MSG_WARNING);
    $AppUI->redirect("m=$m&tab=$tab&sejour_id=0");
  }
  // Ancienne methode
  /*if (!array_key_exists($sejour->praticien_id, $listPraticiens)) {
    $AppUI->setMsg("Vous n'avez pas accés aux séjours du Dr {$sejour->_ref_praticien->_view}", UI_MSG_WARNING);
    $AppUI->redirect("m=$m&tab=$tab&sejour_id=0");
  }*/

  
  foreach ($sejour->_ref_operations as &$operation) {
    $operation->loadRefsFwd();
  }

  foreach ($sejour->_ref_affectations as &$affectation) {
    $affectation->loadRefLit();
    $lit =& $affectation->_ref_lit;
    $lit->loadCompleteView();
  }

  $praticien =& $sejour->_ref_praticien;
  $patient =& $sejour->_ref_patient;
}

$sejour->makeDatesOperations();
$sejour->loadRefsConsultAnesth();
$sejour->_ref_consult_anesth->loadRefConsultation();
$patient->loadRefsSejours();

$patient->loadRefsFwd();
$patient->loadRefsCorrespondants();

$correspondantsMedicaux = array();
if ($patient->_ref_medecin_traitant->_id) {
	$correspondantsMedicaux["traitant"] = $patient->_ref_medecin_traitant;
}
foreach($patient->_ref_medecins_correspondants as $correspondant) {
  $correspondantsMedicaux["correspondants"][] = $correspondant->_ref_medecin;
}

$medecin_adresse_par = "";
if ($sejour->adresse_par_prat_id && ($sejour->adresse_par_prat_id != $patient->_ref_medecin_traitant->_id)) {
  $medecin_adresse_par = new CMedecin();
  $medecin_adresse_par->load($sejour->adresse_par_prat_id);
}

$sejours =& $patient->_ref_sejours;

// Heures & minutes
$config =& $dPconfig["dPplanningOp"]["CSejour"];
$hours = range($config["heure_deb"], $config["heure_fin"]);
$mins = range(0, 59, $config["min_intervalle"]);
$heure_sortie_ambu   = $config["heure_sortie_ambu"];
$heure_sortie_autre  = $config["heure_sortie_autre"];
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];

$sejour->makeCancelAlerts();
$sejour->loadNumDossier();

// Chargement des etablissements externes
$order = "nom";
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, $order);

// Récupération des services
$service = new CService();
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$listServices = $service->loadListWithPerms(PERM_READ,$where, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("urgInstalled", CModule::getInstalled("dPurgences"));
$smarty->assign("heure_sortie_ambu"   , $heure_sortie_ambu);
$smarty->assign("heure_sortie_autre"  , $heure_sortie_autre);
$smarty->assign("heure_entree_veille" , $heure_entree_veille);
$smarty->assign("heure_entree_jour"   , $heure_entree_jour);
//$smarty->assign("locked_sejour"         , $locked_sejour);

$smarty->assign("prestations", $prestations);
$smarty->assign("categorie_prat", $categorie_prat);
$smarty->assign("sejour"        , $sejour);
$smarty->assign("op"            , new COperation);
$smarty->assign("praticien"     , $praticien);
$smarty->assign("patient"       , $patient);
$smarty->assign("sejours"       , $sejours);

$smarty->assign("correspondantsMedicaux", $correspondantsMedicaux);
$smarty->assign("listEtab", $listEtab);
$smarty->assign("medecin_adresse_par", $medecin_adresse_par);

$smarty->assign("etablissements", $etablissements);
$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("listServices"  , $listServices);

$smarty->assign("hours", $hours);
$smarty->assign("mins" , $mins);

$smarty->display("vw_edit_sejour.tpl");

?>