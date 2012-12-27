<?php /*  $ */

/**
 * Request find candidates
 *
 * @category sip
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Récuperation des patients recherchés
$patient_nom        = CValue::request("nom"            , "");
$patient_prenom     = CValue::request("prenom"         , "");
$patient_jeuneFille = CValue::request("nom_jeune_fille", "");
$patient_ville      = CValue::request("ville"          , "");
$patient_cp         = CValue::request("cp"             , "");
$patient_day        = CValue::request("Date_Day"  , "");
$patient_month      = CValue::request("Date_Month", "");
$patient_year       = CValue::request("Date_Year" , "");

$patient = new CPatient();

$receiver_ihe           = new CReceiverIHE();
$receiver_ihe->actif    = 1;
$receiver_ihe->group_id = CGroups::loadCurrent()->_id;
$receivers = $receiver_ihe->loadMatchingList();

$iti_handler = new CITIDelegatedHandler();

$profil      = "PDQ";
$transaction = "ITI21";
$message     = "PDQ";
$code        = "Q22";

foreach ($receivers as $_receiver) {
  if (!$iti_handler->isMessageSupported($transaction, $message, $code, $_receiver)) {
    continue;
  }

  $patient->_receiver = $_receiver;

  // Envoi de l'évènement
  $iti_handler->sendITI($profil, $transaction, $message, $code, $patient);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->display("inc_list_patients.tpl");

CApp::rip();