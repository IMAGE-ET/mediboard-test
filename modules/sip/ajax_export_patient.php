<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
global $can, $dPconfig, $AppUI, $g;

$can->needsAdmin();

$class = mbGetValueFromGet("class");

// Filtre sur les enregistrements
$patient = new CPatient();
$action = mbGetValueFromGet("action", "start");

// Tous les départs possibles
$idMins = array(
  "start"    => "000000",
  "continue" => mbGetValueFromGetOrSession("idContinue"),
  "retry"    => mbGetValueFromGetOrSession("idRetry"),
);

$idMin = mbGetValue(@$idMins[$action], "000000");
mbSetValueToSession("idRetry", $idMin);

// Requêtes
$where = array();
$where[$patient->_spec->key] = "> '$idMin'";

// Bornes
if ($export_id_min = $dPconfig["sip"]["export_id_min"]) {
  $where[] = $patient->_spec->key." >= '$export_id_min'";
}
if ($export_id_max = $dPconfig["sip"]["export_id_max"]) {
  $where[] = $patient->_spec->key." <= '$export_id_max'";
}

// Comptage
$count = $patient->countList($where);
$max = $dPconfig["sip"]["export_segment"];
$max = min($max, $count);
$AppUI->stepAjax("Export de $max sur $count objets de type 'CPatient' à partir de l'ID '$idMin'", UI_MSG_OK);

// Time limit
$seconds = max($max / 20, 120);
$AppUI->stepAjax("Limite de temps du script positionné à '$seconds' secondes", UI_MSG_OK);
set_time_limit($seconds);

// Export réel
$errors = 0;
$patients = $patient->loadList($where, $patient->_spec->key, "0, $max");

foreach ($patients as $patient) {
	$patient->_ref_last_log->type = "create";
	
	$dest_hprim = new CDestinataireHprim();
	
  $dest_hprim->type = "sip";
  $dest_hprim->loadMatchingObject();

  if (!$patient->_IPP) {
    $IPP = new CIdSante400();
    //Paramétrage de l'id 400
    $IPP->object_class = "CPatient";
    $IPP->object_id = $patient->_id;
    $IPP->tag = $dest_hprim->destinataire;
    $IPP->loadMatchingObject();

    $patient->_IPP = $IPP->id400;
  }
	
  $domEvenement = new CHPrimXMLEvenementsPatients();
  $domEvenement->_emetteur = CAppUI::conf('mb_id');
  $domEvenement->_destinataire = $dest_hprim->destinataire;
  $messageEvtPatient = $domEvenement->generateEvenementsPatients($patient);
  
  if (!$messageEvtPatient) {
  	trigger_error("Création de l'événement patient impossible.", E_USER_WARNING);
    $AppUI->stepAjax("Import de '$patient->_view' échoué", UI_MSG_WARNING);
  }
}

// Enregistrement du dernier identifiant dans la session
if (@$patient->_id) {
  mbSetValueToSession("idContinue", $patient->_id);
  $AppUI->stepAjax("Dernier ID traité : '$patient->_id'", UI_MSG_OK);
}

$AppUI->stepAjax("Import terminé avec  '$errors' erreurs", $errors ? UI_MSG_WARNING : UI_MSG_OK);

?>