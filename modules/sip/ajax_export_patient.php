<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
global $can, $g;

$can->needsAdmin();

// Filtre sur les enregistrements
$patient = new CPatient();
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
$where[$patient->_spec->key] = "> '$idMin'";

$sip_config = CAppUI::conf("sip");

// Bornes
if ($export_id_min = $sip_config["export_id_min"]) {
  $where[] = $patient->_spec->key." >= '$export_id_min'";
}
if ($export_id_max = $sip_config["export_id_max"]) {
  $where[] = $patient->_spec->key." <= '$export_id_max'";
}

// Comptage
$count = $patient->countList($where);
$max = $sip_config["export_segment"];
$max = min($max, $count);
CAppUI::stepAjax("Export de $max sur $count objets de type 'CPatient' à partir de l'ID '$idMin'", UI_MSG_OK);

// Time limit
$seconds = max($max / 20, 120);
CAppUI::stepAjax("Limite de temps du script positionné à '$seconds' secondes", UI_MSG_OK);
set_time_limit($seconds);

// Export réel
$errors = 0;
$patients = $patient->loadList($where, $patient->_spec->key, "0, $max");

foreach ($patients as $patient) {
  $patient->loadIPP();
  $patient->loadRefsSejours();
  $patient->_ref_last_log->type = "create";
  $dest_hprim = new CDestinataireHprim();
	
  $dest_hprim->type = "sip";
  $dest_hprim->loadMatchingObject();

  if (!$patient->_IPP) {
    $IPP = new CIdSante400();
    //Paramétrage de l'id 400
    $IPP->object_class = "CPatient";
    $IPP->object_id = $patient->_id;
    $IPP->tag = $dest_hprim->_tag;
    $IPP->loadMatchingObject();

    $patient->_IPP = $IPP->id400;
  }

  if ((CAppUI::conf("sip pat_no_ipp") && $patient->_IPP) || 
      (!CAppUI::conf("sip send_all_patients") && empty($patient->_ref_sejours))) {
  	continue;
  }

  $domEvenement = new CHPrimXMLEnregistrementPatient();
  $domEvenement->emetteur = CAppUI::conf('mb_id');
  $domEvenement->destinataire = $dest_hprim->nom;
  $messageEvtPatient = $domEvenement->generateTypeEvenement($patient);
  $doc_valid = $domEvenement->schemaValidate();
  
  if (!$doc_valid) {
    $errors++;
  	trigger_error("Création de l'événement patient impossible.", E_USER_WARNING);
    CAppUI::stepAjax("Import de '$patient->_view' échoué", UI_MSG_WARNING);
  }
}

// Enregistrement du dernier identifiant dans la session
if (@$patient->_id) {
  CValue::setSession("idContinue", $patient->_id);
  CAppUI::stepAjax("Dernier ID traité : '$patient->_id'", UI_MSG_OK);
}

CAppUI::stepAjax("Import terminé avec  '$errors' erreurs", $errors ? UI_MSG_WARNING : UI_MSG_OK);

?>