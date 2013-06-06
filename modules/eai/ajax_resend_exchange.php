<?php 

/**
 * Resend message
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();
 
$receiver_guid = CValue::get("receiver_guid");

$receiver = CMbObject::loadFromGuid($receiver_guid);
$receiver->loadConfigValues();

if (!$receiver_guid || !$receiver->_id) {
  CAppUI::stepAjax("CInteropReceiver.none", UI_MSG_ERROR); 
}

// On rejoue pour une liste de NDA
if ($list_nda = CValue::get("list_nda")) {
  $ndas = explode("|", $list_nda);
  
  $sejours = array();
  foreach ($ndas as $_nda) {
    $sejour = new CSejour();
    $sejour->loadFromNDA($_nda);

    if ($sejour->_id) {
      $sejours[] = $sejour;
    }
  }
}
else {
  // Filtre sur les enregistrements
  $sejour = new CSejour();
  $action = CValue::get("action", "start");
  
  // Tous les départs possibles
  $idMins = array(
    "start"    => CValue::get("id_start", "000000"),
    "continue" => CValue::getOrSession("idContinue"),
    "retry"    => CValue::getOrSession("idRetry"),
  );
  
  $idMin = CValue::first(@$idMins[$action], "000000");
  CValue::setSession("idRetry", $idMin);
  
  // Requêtes
  $where = array();
  $where[$sejour->_spec->key] = "> '$idMin'";
  $where['annule']            = " = '0'";
  
  $date_min = CValue::getOrSession('date_min', CMbDT::dateTime("-7 day"));
  $date_max = CValue::getOrSession('date_max', CMbDT::dateTime("+1 day"));
  
  // Bornes
  $where['entree'] = " BETWEEN '$date_min' AND '$date_max'";
  
  // Comptage
  $count_sejours = $sejour->countList($where);
  $max           = min(CValue::get("count", 30), $count_sejours);
  CAppUI::stepAjax("Export de $max sur $count_sejours objets de type 'CSejour' à partir de l'ID '$idMin'", UI_MSG_OK);
  
  // Time limit
  $seconds = max($max / 20, 120);
  CAppUI::stepAjax("Limite de temps du script positionné à '$seconds' secondes", UI_MSG_OK);
  CApp::setTimeLimit($seconds);
  
  // Export réel
  $sejours  = $sejour->loadList($where, $sejour->_spec->key, "0, $max");
}

$errors   = 0;
$exchange = 0;
foreach ($sejours as $_sejour) {
  $_sejour->loadRefPraticien();
  $_sejour->loadRefPatient();
  $_sejour->loadNDA($receiver->group_id);
  
  $_sejour->_ref_last_log->type = "create";
  
  if (CValue::get("only_pread") && ($_sejour->_etat != "preadmission")) {
    continue;
  }
      
  if (CValue::get("without_exchanges") && ($_sejour->countExchanges() > 0)) {
    continue;
  }
  
  if ($receiver instanceof CDestinataireHprim) {
    CAppUI::stepAjax("Le traitement pour ce destinataire n'est pas pris en charge", UI_MSG_ERROR); 
  }
  
  if ($receiver instanceof CReceiverHL7v2) {
    $receiver->getInternationalizationCode("ITI31");  
    $_sejour->_receiver = $receiver;
    
    $movement                = new CMovement();
    $movement->sejour_id     = $_sejour->_id;
    $movement->movement_type = CValue::get("movement_type");
    $movements = $movement->loadMatchingList();
    foreach ($movements as $_movement) {
      $code = $_movement->original_trigger_code;

      $iti31 = new CITI31DelegatedHandler();
      if (!$iti31->isMessageSupported("ITI31", "ADT", $code, $receiver)) {
        $errors++;
        CAppUI::stepAjax("Le destinataire ne prend pas en charge cet événement", UI_MSG_WARNING);
      }
      
      $_sejour->_ref_hl7_movement = $_movement;
      
      try {
        // Envoi de l'événement
        $iti31->sendITI("PAM", "ITI31", "ADT", $code, $_sejour);
        $exchange++;
      }
      catch (Exception $e) {
        $errors++;
        CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING);
      }
    }
  }
}
    
// Enregistrement du dernier identifiant dans la session
if (@$_sejour->_id) {
  CValue::setSession("idContinue", $_sejour->_id);
  CAppUI::stepAjax("Dernier ID traité : '$_sejour->_id'", UI_MSG_OK);
  if (!$errors) {
    CAppUI::stepAjax("$exchange de créés", UI_MSG_OK);
  }
}

CAppUI::stepAjax("Import terminé avec  '$errors' erreurs", $errors ? UI_MSG_WARNING : UI_MSG_OK);


