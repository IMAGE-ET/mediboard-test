<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$mode_operation = CValue::get("mode_operation", 0);
$sejour_id      = CValue::get("sejour_id"     , 0);
$patient_id     = CValue::get("patient_id"    , 0);

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

$sejour = new CSejour;
$praticien = new CMediusers;
if ($sejour_id) {
  $sejour->load($sejour_id);
  $sejour->loadRefsFwd();
  $praticien =& $sejour->_ref_praticien;
  $praticien->canDo();
  $patient =& $sejour->_ref_patient;
  $patient->loadRefsSejours();
  $sejours =& $patient->_ref_sejours;
}
else {
  $patient = new CPatient;
  $patient->load($patient_id);
  $patient->loadRefsSejours();
  $sejours =& $patient->_ref_sejours;
}

$sejour->makeDatesOperations();
$sejour->loadNDA();

$sejour->loadRefCurrAffectation()->loadRefService();

$patient->loadRefsFwd();
$patient->loadRefsCorrespondants();
$patient->loadRefsCorrespondantsPatient();

$correspondantsMedicaux = array();
if ($patient->_ref_medecin_traitant->_id) {
  $correspondantsMedicaux["traitant"] = $patient->_ref_medecin_traitant;
}
foreach ($patient->_ref_medecins_correspondants as $correspondant) {
  $correspondantsMedicaux["correspondants"][] = $correspondant->_ref_medecin;
}

$medecin_adresse_par = "";
if ($sejour->adresse_par_prat_id && ($sejour->adresse_par_prat_id != $patient->_ref_medecin_traitant->_id)) {
  $medecin_adresse_par = new CMedecin();
  $medecin_adresse_par->load($sejour->adresse_par_prat_id);
}

// L'utilisateur est-il un praticien
$mediuser = CMediusers::get();

$sortie_sejour = CMbDT::dateTime();
if ($sejour->sortie_reelle) {
  $sortie_sejour = $sejour->sortie_reelle;
}

$where = array();
$where["entree"] = "<= '".$sortie_sejour."'";
$where["sortie"] = ">= '".$sortie_sejour."'";
$where["function_id"] = "IS NOT NULL";

$affectation = new CAffectation();
/** @var CAffectation[] $blocages_lit */
$blocages_lit = $affectation->loadList($where);

$where["function_id"] = "IS NULL";

foreach ($blocages_lit as $key => $blocage) {
  $blocage->loadRefLit()->loadRefChambre()->loadRefService();
  $where["lit_id"] = "= '$blocage->lit_id'";
  if (!$sejour->_id && $affectation->loadObject($where)) {
    $affectation->loadRefSejour();
    $affectation->_ref_sejour->loadRefPatient();
    $blocage->_ref_lit->_view .= " indisponible jusqu'à ".CMbDT::transform($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y")." (".$affectation->_ref_sejour->_ref_patient->_view.")";
  }
}

// Configuration
$config = CAppUI::conf("dPplanningOp CSejour");
$hours = range($config["heure_deb"], $config["heure_fin"]);
$mins = range(0, 59, $config["min_intervalle"]);
$heure_sortie_ambu   = $config["heure_sortie_ambu"];
$heure_sortie_autre  = $config["heure_sortie_autre"];
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];

$config = CAppUI::conf("dPplanningOp COperation");
$hours_duree = range($config["duree_deb"], $config["duree_fin"]);
$hours_urgence = range($config["hour_urgence_deb"], $config["hour_urgence_fin"]);
$mins_duree = range(0, 59, $config["min_intervalle"]);

// Chargement des etablissements externes
$etab = new CEtabExterne();
$count_etab_externe = $etab->countList();

// Récupération des services
$service = new CService();
$where = array();
$where["group_id"]  = "= '".CGroups::loadCurrent()->_id."'";
$where["cancelled"] = "= '0'";
$order = "nom";
$listServices = $service->loadListWithPerms(PERM_READ, $where, $order);
foreach ($listServices as $_service) {
  $_service->loadRefUFSoins();
}

if (CModule::getActive("maternite")) {
  $sejour->loadRefGrossesse();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejours_collision", $patient->getSejoursCollisions());

$smarty->assign("urgInstalled", CModule::getInstalled("dPurgences"));
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("heure_sortie_ambu",   $heure_sortie_ambu);
$smarty->assign("heure_sortie_autre",  $heure_sortie_autre);
$smarty->assign("heure_entree_veille", $heure_entree_veille);
$smarty->assign("heure_entree_jour",   $heure_entree_jour);
$smarty->assign("hours"        , $hours);
$smarty->assign("mins"         , $mins);
$smarty->assign("hours_duree"  , $hours_duree);
$smarty->assign("hours_urgence", $hours_urgence);
$smarty->assign("mins_duree"   , $mins_duree);
$smarty->assign("ufs"          , CUniteFonctionnelle::getUFs());

$smarty->assign("sejour"   , $sejour);
$smarty->assign("op"       , new COperation);
$smarty->assign("praticien", $praticien);
$smarty->assign("patient"  , $patient);
$smarty->assign("sejours"  , $sejours);

$smarty->assign("correspondantsMedicaux", $correspondantsMedicaux);
$smarty->assign("count_etab_externe", $count_etab_externe);
$smarty->assign("medecin_adresse_par", $medecin_adresse_par);

$smarty->assign("listServices"  , $listServices);

$smarty->assign("mode_operation", $mode_operation);
$smarty->assign("etablissements", $etablissements);
$smarty->assign("prestations"   , $prestations);
$smarty->assign("blocages_lit"  , $blocages_lit);
$smarty->display("inc_form_sejour.tpl");
