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
    $results[$i]["function"]    = addslashes(trim($line[0]));
    $results[$i]["motif"]       = addslashes(trim($line[1]));
    $results[$i]["temps_op"]    = addslashes(trim($line[2]));
    $results[$i]["actes"]       = addslashes(trim($line[3]));
    $results[$i]["type_hospi"]  = strtolower((trim($line[4])));
    $results[$i]["duree_hospi"] = addslashes(trim($line[5]));
    
    // Règles pour les types de séjour et les durées
    $results[$i]["type_hospi"]  = $results[$i]["type_hospi"] ? $results[$i]["type_hospi"] : "comp";
    $results[$i]["type_hospi"]  = ($results[$i]["type_hospi"] == "hospi") ? "comp" : $results[$i]["type_hospi"];
    if($results[$i]["type_hospi"] == "ambu") {
      $results[$i]["duree_hospi"] = 0;
    }
    
    $results[$i]["error"] = 0;
    
    // Fonction
    $function = new CFunctions();
    $function->group_id = CGroups::loadCurrent()->_id;
    $function->text     = $results[$i]["function"];
    $function->loadMatchingObject();
    if(!$function->_id) {
      $function->type               = "cabinet";
      $function->color              = "ffffff";
      $function->compta_partagee    = 0;
      $function->consults_partagees = 1;
      $msg = $function->store();
      if($msg) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
        $results[$i]["error"] = $msg;
        $i++;
        continue;
      }
    }
    
    // Protocole
    $protocole = new CProtocole();
    $protocole->for_sejour  = 0;
    $protocole->libelle     = $results[$i]["motif"];
    $protocole->type        = $results[$i]["type_hospi"];
    $protocole->duree_hospi = $results[$i]["duree_hospi"];
    $protocole->function_id = $function->_id;
    $protocole->loadMatchingObject();
    if($protocole->_id) {
      $msg = "Protocole existant";
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["error"] = $msg;
      $i++;
      continue;
    }
    $protocole->temp_operation = intval(($results[$i]["temps_op"] / 60)).":".($results[$i]["temps_op"] % 60).":00";
    $protocole->codes_ccam     = $results[$i]["actes"];
    if($protocole->libelle == "" || $protocole->duree_hospi === "" || $protocole->temp_operation == "") {
      $msg = "Champs manquants";
    } else {
      $msg = $protocole->store();
    }
    if($msg) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["error"] = $msg;
      $i++;
      continue;
    }
    CAppUI::setMsg("Protocole créé", UI_MSG_OK);
    
    $i++;
  }
  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("protocole_dhe_import_csv.tpl");
