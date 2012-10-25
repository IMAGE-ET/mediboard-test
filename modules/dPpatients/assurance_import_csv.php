<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU GPL
 */

CCanDo::checkAdmin();

$file = isset($_FILES['import']) ? $_FILES['import'] : null;

$results = array();
$i = 0;

if ($file && ($fp = fopen($file['tmp_name'], 'r'))) {
  // Object columns on the first line
  $cols = fgetcsv($fp, null, ";");

  // Each line
  while($line = fgetcsv($fp, null, ";")) {
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }

    // Parsing
    $results[$i]["codeCDM"]   = addslashes(trim($line[0]));
    $results[$i]["nom"]       = addslashes(trim($line[1]));
    $results[$i]["adress"]    = addslashes(trim($line[2]));
    $results[$i]["rue"]       = addslashes(trim($line[3]));
    $results[$i]["localite"]  = addslashes(trim($line[4]));
    $results[$i]["ean"]       = addslashes(trim($line[9]));
    $results[$i]["error"]     = 0;

    // Fonction
    $corres = new CCorrespondantPatient();
    $corres->ean = $results[$i]["ean"];
    $corres->relation     = "assurance";
    $corres->loadMatchingObject();


    if($corres->_id) {
      //update

      $corres->nom = $results[$i]["nom"];
      $corres->adresse = $results[$i]["rue"];
      $corres->ville = $results[$i]["localite"];

      if($corres->ean == "" || $corres->nom == "") {
        $msg = "CCorrespondant-import-missing";
      }
      else {
        $msg = $corres->store();
      }

      if($msg) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
        $results[$i]["error"] = $msg;
        $i++;
        continue;
      }

    } else {
      // create
      if($corres->nom  && $corres->ean ) {
        $msg = $corres->store();
      } else {
        $msg="CCorrespondant-import-missing";
      }

      if($msg) {
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
