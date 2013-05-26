<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Réalisation
$event_ids = CValue::post("realise_ids");
$event_ids = $event_ids ? explode("|", $event_ids) : array();
foreach ($event_ids as $_event_id) {
  $evenement = new CEvenementSSR();
  $evenement->load($_event_id);
  
  // Autres rééducateurs
  global $can;
  if ($evenement->therapeute_id !=  CAppUI::$instance->user_id && !$can->admin) {
    CAppUI::displayMsg("Impossible de modifier les événements d'un autre rééducateur", "CEvenementSSR-msg-modify");
    continue;
  }
  
  // Suppression des evenements SSR
  $evenement->realise = "1";
  $evenement->annule  = "0";
  $evenement->_traitement = "1";
  $msg = $evenement->store();
  CAppUI::displayMsg($msg, "CEvenementSSR-msg-modify");
}

// Annulation
$event_ids = CValue::post("annule_ids");
$event_ids = $event_ids ? explode("|", $event_ids) : array();
foreach ($event_ids as $_event_id) {
  $evenement = new CEvenementSSR();
  $evenement->load($_event_id);
  
  // Autres rééducateurs
  global $can;
  if ($evenement->therapeute_id !=  CAppUI::$instance->user_id && !$can->admin) {
    CAppUI::displayMsg("Impossible de modifier les événements d'un autre rééducateur", "CEvenementSSR-msg-modify");
    continue;
  }
  
  // Suppression des evenements SSR
  $evenement->realise = "0";
  $evenement->annule  = "1";
  $evenement->_traitement = "1";
  $msg = $evenement->store();
  CAppUI::displayMsg($msg, "CEvenementSSR-msg-modify");
}

echo CAppUI::getMsg();
CApp::rip();
