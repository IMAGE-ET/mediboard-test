<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$file = isset($_FILES['import']) ? $_FILES['import'] : null;

$results = array();
$i = 0;

if ($file && ($fp = fopen($file['tmp_name'], 'r'))) {
  // Object columns on the first line
  $cols = fgetcsv($fp, null, ";");

  // Each line
  while ($line = fgetcsv($fp, null, ";")) {
    if (!isset($line[1]) || $line[1] == "") {
      continue;
    }
    // Parsing
    $results[$i]["codeCDM"]   = addslashes(trim($line[0]));   //not used
    $results[$i]["nom"]       = addslashes(trim($line[1]));
    $results[$i]["adress"]    = addslashes(trim($line[2]));
    $results[$i]["rue"]       = addslashes(trim($line[3]));
    $explode                  = explode(" ", addslashes(trim($line[4])), 2);
    $results[$i]["cp"]        = $explode[0];
    $results[$i]["localite"]  = $explode[1];
    $results[$i]["pec"]       = addslashes(trim($line[5]));
    $results[$i]["ean"]       = addslashes(trim($line[9]));
    $results[$i]["error"]     = 0;

    // Fonction
    $corres = new CCorrespondantPatient();
    $corres->ean = $results[$i]["ean"];
    $corres->nom = $results[$i]["nom"];
    $corres->relation     = "assurance";
    $corres->loadMatchingObject();

    if ($corres->_id) {
      //update

      $corres->nom = $results[$i]["nom"];
      $corres->adresse = $results[$i]["rue"];
      $corres->cp = $results[$i]["cp"];
      $corres->ville = $results[$i]["localite"];
      $corres->type_pec = $results[$i]["pec"];

      if ($corres->ean == "" || $corres->nom == "") {
        $msg = "CCorrespondant-import-missing1";
      }
      else {
        $msg = $corres->store();
        if (!$msg) {
          $msg = "update";
        }
      }

      if ($msg) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
        $results[$i]["error"] = $msg;
        $i++;
        continue;
      }
    }
    else {
      // create
      $corres->nom = $results[$i]["nom"];
      $corres->adresse = $results[$i]["rue"];
      $corres->cp = $results[$i]["cp"];
      $corres->ville = $results[$i]["localite"];
      $corres->type_pec = $results[$i]["pec"];
      $corres->ean = $results[$i]["ean"];

      if (!$corres->nom  && !$corres->ean ) {
        $msg="CCorrespondant-import-missing2";
        continue;
      }
      $msg = $corres->store();

      if ($msg) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
        $results[$i]["error"] = $msg;
        $i++;
        continue;
      }
    }


    CAppUI::setMsg("CCorrespondant-treated-import", UI_MSG_OK);

    $i++;
  }
  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("assurance_import_csv.tpl");
