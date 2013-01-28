<?php

/**
 * Request find candidates
 *
 * @category SIP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: ajax_refresh_exchange.php 15880 2012-06-15 08:14:36Z phenxdesign $
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Récuperation des patients recherchés
$patient_nom              = CValue::request("nom");
$patient_prenom           = CValue::request("prenom");
$patient_jeuneFille       = CValue::request("nom_jeune_fille");
$patient_sexe             = CValue::request("sexe");
$patient_ville            = CValue::request("ville");
$patient_cp               = CValue::request("cp");
$patient_day              = CValue::request("Date_Day");
$patient_month            = CValue::request("Date_Month");
$patient_year             = CValue::request("Date_Year");
$quantity_limited_request = CValue::request("quantity_limited_request");
$pointer                  = CValue::request("pointer");

$patient_naissance = null;
if (($patient_year) || ($patient_month) || ($patient_day)) {
  $patient_naissance = "on";
}

$naissance = null;
if ($patient_naissance == "on") {
  $year =($patient_year)?"$patient_year-":"%-";
  $month =($patient_month)?"$patient_month-":"%-";
  $day =($patient_day)?"$patient_day":"%";
  if ($day!="%") {
    $day = str_pad($day, 2, "0", STR_PAD_LEFT);
  }

  $naissance = $year.$month.$day;
}

$patient                  = new CPatient();
$patient->nom             = $patient_nom;
$patient->prenom          = $patient_prenom;
$patient->nom_jeune_fille = $patient_jeuneFille;
$patient->naissance       = $naissance;
$patient->ville           = $patient_ville;
$patient->cp              = $patient_cp;
$patient->sexe            = $patient_sexe;

$receiver_ihe           = new CReceiverIHE();
$receiver_ihe->actif    = 1;
$receiver_ihe->group_id = CGroups::loadCurrent()->_id;
$receivers = $receiver_ihe->loadMatchingList();

$profil      = "PDQ";
$transaction = "ITI21";
$message     = "QBP";
$code        = "Q22";

$ack_data    = null;

$iti_handler = new CITIDelegatedHandler();
foreach ($receivers as $_receiver) {
  if (!$iti_handler->isMessageSupported($transaction, $message, $code, $_receiver)) {
    continue;
  }

  $patient->_receiver                 = $_receiver;
  $patient->_quantity_limited_request = $quantity_limited_request;
  $patient->_pointer                  = $pointer;

  // Envoi de l'évènement
  $ack_data = $iti_handler->sendITI($profil, $transaction, $message, $code, $patient);
}

$patients = array();
$pointer  = null;

if ($ack_data) {
  $ack_event = new CHL7v2EventQBPK22();
  $patients  = $ack_event->handle($ack_data)->handle();

  if (array_key_exists("pointer", $patients)) {
    $pointer = $patients["pointer"];
  }

  unset($patients["pointer"]);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("patient"                 , $patient);
$smarty->assign("patients"                , $patients);
$smarty->assign("quantity_limited_request", $quantity_limited_request);
$smarty->assign("pointer"                 , $pointer);
$smarty->display("inc_list_patients.tpl");

CApp::rip();