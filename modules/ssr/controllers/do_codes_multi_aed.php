<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

$token_evts  = CValue::post("token_evts");
$evenement_ids = explode("|", $token_evts);

// Recuperation des codes cdarrs a ajouter et a supprimer aux evenements
$added_codes = CValue::post("added_codes") ? explode("|", CValue::post("added_codes")) : '';
$remed_codes = CValue::post("remed_codes") ? explode("|", CValue::post("remed_codes")) : '';
$other_codes = CValue::post("_codes");

$codes = array();
if ($added_codes) {
  $codes["add"] = $added_codes;
}
if ($remed_codes) {
  $codes["rem"] = $remed_codes;
}

// Ajout des codes rajoutés depuis l'autocomplete
if (count($other_codes)) {
  foreach ($other_codes as $_other_cdarr) {
    $codes["add"][] = $_other_cdarr;
  }
}

foreach ($evenement_ids as $_evenement_id) {
  $evenement = new CEvenementSSR;
  $evenement->load($_evenement_id);
  
  // Autres rééducateurs
  global $can;
  $therapeute_id = $evenement->therapeute_id;
  if ($evenement->seance_collective_id) {
    $therapeute_id = $evenement->loadRefSeanceCollective()->therapeute_id;
  }
  if ($therapeute_id && ($therapeute_id != CAppUI::$instance->user_id) && !$can->admin) {
    CAppUI::displayMsg("Impossible de modifier les événements d'un autre rééducateur", "CEvenementSSR-msg-modify");
    continue;
  }

  // Actes par code pour chaque événement
  $actes_by_code = array();
  foreach ($evenement->loadRefsActes() as $type => $_actes) {
    foreach ($_actes as $_acte) {
      $actes_by_code[$_acte->code][$_acte->_id] = $_acte;
    }
  }

  foreach ($codes as $action => $_codes) {
    foreach ($_codes as $_code) {
      // Ajout de l'acte a tous les évènements
      if ($action == "add") {
        if (!isset($actes_by_code[$_code])) {
          $acte = strlen($_code) == 7 ? new CActeCsARR() : new CActeCdARR();
          $acte->evenement_ssr_id = $_evenement_id;
          $acte->code = $_code;

          $msg = $acte->store();
          CAppUI::displayMsg($msg, "$acte->_class-msg-create");
        }
      }

      // Suppression de l'acte pour tous les évènements
      if ($action == "rem") {
        if (isset($actes_by_code[$_code])) {
          foreach ($actes_by_code[$_code] as $_acte) {
            $msg = $_acte->delete();
            CAppUI::displayMsg($msg, "$_acte->_class-msg-delete");
          }
        }
      }
    }

  }
}

echo CAppUI::getMsg();
CApp::rip();