<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU GPL
 */

CCanDo::checkAdmin();

$file = CValue::files("import");
$dryrun = CValue::post("dryrun");

$results = array();
$unfound = array();
$i = 0;

if ($file && ($fp = fopen($file['tmp_name'], 'r'))) {
  // Object columns on the first line
  $cols = fgetcsv($fp, null, ";");

  // Each line
  while ($line = fgetcsv($fp, null, ";")) {
    $i++;
    
    // Skip empty lines
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }
    
    // Parsing
    $line = array_map("trim"      , $line);
    $line = array_map("addslashes", $line);
    $results[$i]["function_name"]       = CMbArray::get($line,  0);
    $results[$i]["praticien_lastname"]  = CMbArray::get($line,  1);
    $results[$i]["praticien_firstname"] = CMbArray::get($line,  2);
    $results[$i]["motif"]               = CMbArray::get($line,  3);
    $results[$i]["temp_operation"]      = CMbArray::get($line,  4);
    $results[$i]["codes_ccam"]          = CMbArray::get($line,  5);
    $results[$i]["type_hospi"]          = CMbArray::get($line,  6);
    $results[$i]["duree_hospi"]         = CMbArray::get($line,  7);
    $results[$i]["duree_uscpo"]         = CMbArray::get($line,  8);
    $results[$i]["duree_preop"]         = CMbArray::get($line,  9);
    $results[$i]["presence_preop"]      = CMbArray::get($line, 10);
    $results[$i]["presence_postop"]     = CMbArray::get($line, 11);
    $results[$i]["uf_hebergement"]      = CMbArray::get($line, 12);
    $results[$i]["uf_medicale"]         = CMbArray::get($line, 13);
    $results[$i]["uf_soins"]            = CMbArray::get($line, 14);
    
    // Type d'hopistalisation
    $results[$i]["type_hospi"] = CValue::first(strtolower($results[$i]["type_hospi"]), "comp");
    if ($results[$i]["type_hospi"] == "hospi") {
      $results[$i]["type_hospi"] = "comp";
    }
    if ($results[$i]["type_hospi"] == "ambu") {
      $results[$i]["duree_hospi"] = 0;
    }
    
    $results[$i]["errors"] = array();
    
    // Fonction
    $function = new CFunctions();
    $function->group_id = CGroups::loadCurrent()->_id;
    $function->text     = $results[$i]["function_name"];
    $function->loadMatchingObject();
    if (!$function->_id) {
      $results[$i]["errors"][] = "Fonction non trouvée";
    }
    
    // Praticien
    $prat = new CMediusers();
    $lastname  = $results[$i]["praticien_lastname"];
    $firstname = $results[$i]["praticien_firstname"];
    if ($lastname) {
      $ljoin = array();
      $ljoin["users"] = "users.user_id = users_mediboard.user_id";
      $where = array();
      $where["users.user_last_name"]  = "= '$lastname'";
      $where["users.user_first_name"] = "= '$firstname'";
      $where["users_mediboard.function_id"] = "= '$function->_id'";
      $prat->loadObject($where, null, null, $ljoin);
      if (!$prat->_id) {
        $results[$i]["errors"][] = "Utilisateur non trouvé";
        $unfound["praticien_lastname"][$lastname] = true;
      }
    }

    // Protocole
    $protocole = new CProtocole();
    $protocole->_hour_op    = null;
    $protocole->_min_op     = null;
    $protocole->for_sejour  = 0;
    $protocole->libelle     = $results[$i]["motif"];
    if ($prat->_id) {
      $protocole->chir_id = $prat->_id;
    } else {
      $protocole->function_id = $function->_id;
    }
    
    // Mise à jour du protocole éventuel existant
    
    $protocole->loadMatchingObject();
    
    $protocole->type            = $results[$i]["type_hospi"];
    $protocole->duree_hospi     = $results[$i]["duree_hospi"];
    $protocole->temp_operation  = $results[$i]["temp_operation"] . ":00";
    $protocole->codes_ccam      = $results[$i]["codes_ccam"];
    $protocole->duree_uscpo     = $results[$i]["duree_uscpo"];
    $protocole->duree_preop     = $results[$i]["duree_preop"]     ? $results[$i]["duree_preop"]     . ":00" : "";
    $protocole->presence_preop  = $results[$i]["presence_preop"]  ? $results[$i]["presence_preop"]  . ":00" : "";
    $protocole->presence_postop = $results[$i]["presence_postop"] ? $results[$i]["presence_postop"] . ":00" : "";
    
    // UF Hébergement
    if ($uf_hebergement = $results[$i]["uf_hebergement"]) {
      $uf = new CUniteFonctionnelle();
      $uf->code = $uf_hebergement;
      $uf->type = "hebergement";
      $uf->loadMatchingObject();
      if ($uf->_id) {
        $protocole->uf_hebergement_id = $uf->_id ? $uf->_id : "";
      }
      else {
        $results[$i]["errors"][] = "UF hébergement non trouvée";
        $unfound["uf_hebergement"][$uf_hebergement] = true;
          
      }
    }
    
    // UF Médicale
    if ($uf_medicale = $results[$i]["uf_medicale"]) {
      $uf = new CUniteFonctionnelle();
      $uf->code = $uf_medicale;
      $uf->type = "medicale";
      $uf->loadMatchingObject();
      if ($uf->_id) {
        $protocole->uf_medicale_id = $uf->_id ? $uf->_id : "";
      }
      else {
        $results[$i]["errors"][] = "UF médicale non trouvée";
        $unfound["uf_medicale"][$uf_medicale] = true;
      }
    }
    
    
    // UF Soins
    if ($uf_soins = $results[$i]["uf_soins"]) {
      $uf = new CUniteFonctionnelle();
      $uf->code = $uf_soins;
      $uf->type = "soins";
      $uf->loadMatchingObject();
      if ($uf->_id) {
        $protocole->uf_soins_id = $uf->_id ? $uf->_id : "";
      }
      else {
        $results[$i]["errors"][] = "UF de soins non trouvée";        
        $unfound["uf_soins"][$uf_soins] = true;
      }
    }
    
    // Field check final
    if ($protocole->libelle == "" || $protocole->duree_hospi === "" || $protocole->temp_operation == "") {
      $results[$i]["errors"][] = "Champs manquants";
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
    $protocole->unescapeValues();
    $existing = $protocole->_id;
    if ($msg = $protocole->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["errors"][] = $msg;
      continue;
    }
    
    CAppUI::setMsg($existing ? "CProtocole-msg-modify" : "CProtocole-msg-create", UI_MSG_OK);
  }

  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("protocole_dhe_import_csv.tpl");
