<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7346 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
global $can, $g;

$can->needsAdmin();

// Filtre sur les enregistrements
$sejour = new CSejour();
$action = CValue::get("action", "start");

// Tous les départs possibles
$idMins = array(
  "start"    => "000000",
  "continue" => CValue::getOrSession("idContinue"),
  "retry"    => CValue::getOrSession("idRetry"),
);

$idMin = CValue::first(@$idMins[$action], "000000");
CValue::setSession("idRetry", $idMin);

// Requêtes
$where = array();
$where[$sejour->_spec->key] = "> '$idMin'";

$sip_config = CAppUI::conf("sip");

// Bornes
if ($export_id_min = $sip_config["export_id_min"]) {
  $where[] = $sejour->_spec->key." >= '$export_id_min'";
}
if ($export_id_max = $sip_config["export_id_max"]) {
  $where[] = $sejour->_spec->key." <= '$export_id_max'";
}

// Comptage
$count = $sejour->countList($where);
$max = $sip_config["export_segment"];
$max = min($max, $count);
CAppUI::stepAjax("Export de $max sur $count objets de type 'CSejour' à partir de l'ID '$idMin'", UI_MSG_OK);

// Time limit
$seconds = max($max / 20, 120);
CAppUI::stepAjax("Limite de temps du script positionné à '$seconds' secondes", UI_MSG_OK);
set_time_limit($seconds);

// Export réel
$errors = 0;
$sejours = $sejour->loadList($where, $sejour->_spec->key, "0, $max");

foreach ($sejours as $sejour) {
  $sejour->loadRefPraticien();
  $sejour->loadRefPatient();
  $sejour->_ref_patient->loadIPP();
  if ($sejour->_ref_prescripteurs) {
    $sejour->loadRefsPrescripteurs();
  }
  $sejour->loadRefAdresseParPraticien();
  $sejour->_ref_patient->loadRefsFwd();
  $sejour->loadRefsActes();
  foreach ($sejour->_ref_actes_ccam as $actes_ccam) {
    $actes_ccam->loadRefPraticien();
  }
  $sejour->loadRefsAffectations();
  $sejour->loadNumDossier();
  $sejour->loadLogs();
  $sejour->loadRefsConsultations();
  $sejour->loadRefsConsultAnesth();
      
  $sejour->_ref_last_log->type = "create";
  $dest_hprim = new CDestinataireHprim();
  
  $dest_hprim->type = "sip";
  $dest_hprim->loadMatchingObject();

  if (!$sejour->_num_dossier) {
    $num_dossier = new CIdSante400();
    //Paramétrage de l'id 400
    $num_dossier->object_class = "CSejour";
    $num_dossier->object_id = $num_dossier->_id;
    $num_dossier->tag = CAppUI::conf('mb_id')." group:$dest_hprim->group_id";
    $num_dossier->loadMatchingObject();

    $num_dossier->$num_dossier = $num_dossier->id400;
  }
  
  if (CAppUI::conf("sip sej_no_numdos") && $sejour->_num_dossier) {
    continue;
  }
  
  $domEvenement = new CHPrimXMLVenuePatient();
  $domEvenement->emetteur = CAppUI::conf('mb_id');
  $domEvenement->destinataire = $dest_hprim->destinataire;
  $messageEvtPatient = $domEvenement->generateTypeEvenement($sejour);
  
  if (!$messageEvtPatient) {
    trigger_error("Création de l'événement patient impossible.", E_USER_WARNING);
    CAppUI::stepAjax("Import de '$sejour->_view' échoué", UI_MSG_WARNING);
  }
}

// Enregistrement du dernier identifiant dans la session
if (@$sejour->_id) {
  CValue::setSession("idContinue", $sejour->_id);
  CAppUI::stepAjax("Dernier ID traité : '$sejour->_id'", UI_MSG_OK);
}

CAppUI::stepAjax("Import terminé avec  '$errors' erreurs", $errors ? UI_MSG_WARNING : UI_MSG_OK);

?>