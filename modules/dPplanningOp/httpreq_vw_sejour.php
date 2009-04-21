<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $tab, $dPconfig;

$mode_operation = mbGetValueFromGet("mode_operation", 0);
$sejour_id      = mbGetValueFromGet("sejour_id"     , 0);
$patient_id     = mbGetValueFromGet("patient_id"    , 0);

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

$sejour = new CSejour;
$praticien = new CMediusers;
if($sejour_id) {
  $sejour->load($sejour_id);
  $sejour->loadRefsFwd();
  $praticien =& $sejour->_ref_praticien;
  $patient =& $sejour->_ref_patient;
  $patient->loadRefsSejours();
  $sejours =& $patient->_ref_sejours;
} else {
  $patient = new CPatient;
  $patient->load($patient_id);
  $patient->loadRefsSejours();
  $sejours =& $patient->_ref_sejours;
}

$sejour->makeDatesOperations();
$sejour->loadNumDossier();

// L'utilisateur est-il un praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

// Vérification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);

// Configuration
$config =& $dPconfig["dPplanningOp"]["CSejour"];
$hours = range($config["heure_deb"], $config["heure_fin"]);
$mins = range(0, 59, $config["min_intervalle"]);
$heure_sortie_ambu   = $config["heure_sortie_ambu"];
$heure_sortie_autre  = $config["heure_sortie_autre"];
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];

$config =& $dPconfig["dPplanningOp"]["COperation"];
$hours_duree = range($config["duree_deb"], $config["duree_fin"]);
$hours_urgence = range($config["hour_urgence_deb"], $config["hour_urgence_fin"]);
$mins_duree = range(0, 59, $config["min_intervalle"]);

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

$smarty->assign("sejour"   , $sejour);
$smarty->assign("op"       , new COperation);
$smarty->assign("praticien", $praticien);
$smarty->assign("patient"  , $patient);
$smarty->assign("sejours"  , $sejours);

$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("mode_operation", $mode_operation);
$smarty->assign("etablissements", $etablissements);
$smarty->assign("prestations"   , $prestations   );
$smarty->display("inc_form_sejour.tpl");

?>