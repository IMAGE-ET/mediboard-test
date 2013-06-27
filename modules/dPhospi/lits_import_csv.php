<?php /* $Id: prat_import_csv.php 6103 2009-04-16 13:36:52Z yohann $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6153 $
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
  while ($line = fgetcsv($fp, null, ";")) {
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }
    
    // Parsing
    $results[$i]["service"]    = addslashes(trim($line[0]));
    $results[$i]["chambre"]    = addslashes(trim($line[1]));
    $results[$i]["nom"]        = addslashes(trim($line[2]));
    $results[$i]["nomcomplet"] = @addslashes(trim($line[3]));
    
    $results[$i]["error"] = 0;
    
    // Service
    $service = new CService();
    $service->nom      = $results[$i]["service"];
    $service->group_id = CGroups::loadCurrent()->_id;
    $service->loadMatchingObject();
    if (!$service->_id) {
      $service->urgence     = 0;
      $service->uhcd        = 0;
      $service->hospit_jour = 0;
      $service->externe     = 0;
      $service->cancelled   = 0;
      $msg = $service->store();
      if ($msg) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
        $results[$i]["error"] = $msg;
        $i++;
        continue;
      }
      CAppUI::setMsg("Service créé", UI_MSG_OK);
    }
    
    // Chambre
    $chambre = new CChambre();
    $chambre->nom        = $results[$i]["chambre"];
    $chambre->service_id = $service->_id;
    $chambre->loadMatchingObject();
    if (!$chambre->_id) {
      $chambre->lits_alpha = 0;
      $chambre->annule     = 0;
      $msg = $chambre->store();
      if ($msg) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
        $results[$i]["error"] = $msg;
        $i++;
        continue;
      }
      CAppUI::setMsg("Chambre créée", UI_MSG_OK);
    }
    
    // Lit
    
    $lit = new CLit();
    $lit->nom         = $results[$i]["nom"];
    $lit->chambre_id  = $chambre->_id;
    $lit->loadMatchingObject();
    $lit->nom_complet = $results[$i]["nomcomplet"];
    if ($lit->_id) {
      $msg = "Lit existant";
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["error"] = $msg;
      $i++;
      continue;
    }
    $msg = $lit->store();
    if ($msg) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["error"] = $msg;
      $i++;
      continue;
    }
    CAppUI::setMsg("Lit créé", UI_MSG_OK);
    
    $i++;
  }
  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("lits_import_csv.tpl");
