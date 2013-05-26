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

$token_evts  = CValue::post("token_evts");
$_evenement_ids = explode("|", $token_evts);

// Recuperation des codes cdarrs a ajouter et a supprimer aux evenements
$add_cdarrs = CValue::post("add_cdarrs") ? explode("|", CValue::post("add_cdarrs")) : '';
$remove_cdarrs = CValue::post("remove_cdarrs") ? explode("|", CValue::post("remove_cdarrs")) : '';
$other_cdarrs = CValue::post("_cdarrs");

$cdarrs = array();
if ($add_cdarrs) {
  $cdarrs["add"] = $add_cdarrs;
}
if ($remove_cdarrs) {
  $cdarrs["remove"] = $remove_cdarrs;
}

// Ajout des codes rajoutés depuis l'autocomplete
if (count($other_cdarrs)) {
  foreach ($other_cdarrs as $_other_cdarr) {
    $cdarrs["add"][] = $_other_cdarr;
  }
}

foreach ($_evenement_ids as $_evenement_id) {
  $evenement = new CEvenementSSR;
  $evenement->load($_evenement_id);
  
  // Autres rééducateurs
  global $can;
  if ($evenement->therapeute_id && ($evenement->therapeute_id != CAppUI::$instance->user_id) && !$can->admin) {
    CAppUI::displayMsg("Impossible de modifier les événements d'un autre rééducateur", "CEvenementSSR-msg-modify");
    continue;
  }
  
  if (is_array($cdarrs)) {
    foreach ($cdarrs as $action => $_cdarrs) {
      foreach ($_cdarrs as $_cdarr) {
        $acte_cdarr = new CActeCdARR();
        $acte_cdarr->evenement_ssr_id = $_evenement_id;
        $acte_cdarr->code = $_cdarr;
        $acte_cdarr->loadMatchingObject();

        // Ajout de l'acte a tous les evenements
        if ($action == "add") {
          if (!$acte_cdarr->_id) {
            $msg = $acte_cdarr->store();
            CAppUI::displayMsg($msg, "CActeCdARR-msg-create");
          }
        }
        // Suppression de l'acte pour tous les evenements
        else {
          if ($acte_cdarr->_id) {
            $msg = $acte_cdarr->delete();
            CAppUI::displayMsg($msg, "CActeCdARR-msg-delete");
          }
        }
      }
    }
  }
}

echo CAppUI::getMsg();
CApp::rip();
