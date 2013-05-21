<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();

$file = CValue::files("import");
$dryrun = CValue::post("dryrun");
$facture_class = CValue::post("facture_class");
if (!$facture_class) {
  $facture_class = CValue::get("facture_class");
}

$results = array();
$unfound = array();
$i = 0;

if ($file && ($fp = fopen($file['tmp_name'], 'r'))) {
  // Each line
  while ($line = fgetcsv($fp, null, ";")) {
    $i++;

    // Skip empty lines
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }

    // Parsing
    $line = array_map("trim"      , $line);
    if (strlen($line[0]) < 100) {
      continue;
    }
    $line = array_map("addslashes", $line);
    $line = $line[0];
    $results[$i]["genre"]           = substr($line, 0, 3);
    $results[$i]["num_client"]      = substr($line, 3, 9);
    $results[$i]["reference"]       = substr($line, 12, 27);
    $results[$i]["montant"]         = substr($line, 39, 10);
    $results[$i]["ref_depot"]       = substr($line, 49, 10);
    $results[$i]["date_depot"]      = substr($line, 59, 6);
    $results[$i]["date_traitement"] = substr($line, 65, 6);
    $results[$i]["date_inscription"]= substr($line, 71, 6);
    $results[$i]["num_microfilm"]   = substr($line, 77, 9);
    $results[$i]["code_rejet"]      = substr($line, 86, 1);
    $results[$i]["reserve"]         = substr($line, 87, 9);
    $results[$i]["prix"]            = substr($line, 96, 4);

    $results[$i]["errors"] = array();


    if (!$results[$i]["reference"]) {
      $results[$i]["errors"][] = "Le numéro de référence n'est pas défini";
    }
    else {
      //Facture d'établissement
      $facture = new $facture_class;
      $facture->num_reference = $results[$i]["reference"];
      $facture->loadMatchingObject();

      if (!$facture->_id) {
        $results[$i]["errors"][] = "Facture introuvable";
      }

      $reglement = new CReglement();
      $reglement->mode = "BVR";
      $reglement->object_id    = $facture->_id;
      $reglement->object_class = $facture->_class;
      $reglement->reference    = $results[$i]["reference"];
      //A voir / vérifier !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
      $reglement->emetteur     = "patient";
      $date = $results[$i]["date_depot"];
      $reglement->date         = "20".substr($date, 0, 2)."-".substr($date, 2, 2)."-".substr($date, 4, 2)." 00:00:00";
      $montant = $results[$i]["montant"];
      $reglement->montant      = substr($montant, 0, 8).",".substr($montant, 8, 2);

      // Field check final
      if ($reglement->montant == "") {
        $results[$i]["errors"][] = "Montant manquant";
      }
      if ($reglement->date == "") {
        $results[$i]["errors"][] = "Date de dépot manquant";
      }
      if ($facture->patient_date_reglement) {
        $results[$i]["errors"][] = "La facture est déjà acquittée";
      }
      // No store on errors
      if (count($results[$i]["errors"])) {
        continue;
      }

      // Dry run to check references
      if ($dryrun) {
        continue;
      }

      // Creation
      $existing = $reglement->_id;
      if ($msg = $reglement->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
        $results[$i]["errors"][] = $msg;
        continue;
      }
      CAppUI::setMsg($existing ? "CReglement-msg-modify" : "CReglement-msg-create", UI_MSG_OK);
    }
  }

  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);
$smarty->assign("facture_class", $facture_class);

$smarty->display("vw_rapprochement_banc.tpl");
