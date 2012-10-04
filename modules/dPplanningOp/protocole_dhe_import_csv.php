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
    $results[$i]["nom"]         = addslashes(trim($line[1]));
    $results[$i]["prenom"]      = addslashes(trim($line[2]));
    $results[$i]["motif"]       = addslashes(trim($line[3]));
    $results[$i]["temps_op"]    = addslashes(trim($line[4]));
    $results[$i]["actes"]       = addslashes(trim($line[5]));
    $results[$i]["type_hospi"]  = strtolower(trim($line[6]));
    $results[$i]["duree_hospi"] = addslashes(trim($line[7]));
    $results[$i]["duree_uscpo"] = addslashes(trim($line[8]));
    $results[$i]["duree_preop"] = addslashes(trim($line[9]));
    $results[$i]["presence_preop"]  = addslashes(trim($line[10]));
    $results[$i]["presence_postop"] = addslashes(trim($line[11]));
    $results[$i]["uf_hebergement"]  = addslashes(trim($line[12]));
    $results[$i]["uf_medicale"] = addslashes(trim($line[13]));
    $results[$i]["uf_soins"]    = addslashes(trim($line[14]));
    
    // Règles pour les types de séjour et les durées
    $results[$i]["type_hospi"]  = $results[$i]["type_hospi"] ? $results[$i]["type_hospi"] : "comp";
    $results[$i]["type_hospi"]  = ($results[$i]["type_hospi"] == "hospi") ? "comp" : $results[$i]["type_hospi"];
    if ($results[$i]["type_hospi"] == "ambu") {
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
    
    $user = new CMediusers();
    // Praticien
    if ($results[$i]["nom"]) {
      $ljoin = array();
      $ljoin["users"] = "users.user_id = users_mediboard.user_id";
      $where = array();
      $where["users.user_last_name"]  = "= '".$results[$i]["nom"]."'";
      $where["users.user_first_name"] = "= '".$results[$i]["prenom"]."'";
      $where["users_mediboard.function_id"] = "= '".$function->_id."'";
      $user->loadObject($where, null, null, $ljoin);
      if(!$user->_id) {
        CAppUI::setMsg("Utilisateur non trouvé", UI_MSG_ERROR);
        $results[$i]["error"] = "Utilisateur non trouvé";
        $i++;
        continue;
      }
    }
    
    // Protocole
    $protocole = new CProtocole();
    $protocole->for_sejour  = 0;
    $protocole->libelle     = $results[$i]["motif"];
    
    if($user->_id) {
      $protocole->chir_id = $user->_id;
    } else {
      $protocole->function_id = $function->_id;
    }
    
    // Mise à jour du protocole éventuel existant
    $protocole->loadMatchingObject();
    
    $protocole->type        = $results[$i]["type_hospi"];
    $protocole->duree_hospi = $results[$i]["duree_hospi"];
    $protocole->temp_operation  = $results[$i]["temps_op"] . ":00";
    $protocole->_hour_op = $protocole->_min_op = null;
    $protocole->codes_ccam      = $results[$i]["actes"];
    $protocole->duree_uscpo     = $results[$i]["duree_uscpo"];
    $protocole->duree_preop     = $results[$i]["duree_preop"] ? $results[$i]["duree_preop"] . ":00" : "";
    $protocole->presence_preop  = $results[$i]["presence_preop"] ? $results[$i]["presence_preop"] . ":00" : "";
    $protocole->presence_postop = $results[$i]["presence_postop"] ? $results[$i]["presence_postop"] . ":00" : "";
    
    // Recherche d'UFS
    // - Hébergement
    $uf = new CUniteFonctionnelle();
    $uf->code = $results[$i]["uf_hebergement"];
    $uf->loadMatchingObject();
    $protocole->uf_hebergement_id = $uf->_id ? $uf->_id : "";
    
    // - Médicale
    $uf = new CUniteFonctionnelle();
    $uf->code = $results[$i]["uf_medicale"];
    $uf->loadMatchingObject();
    $protocole->uf_medicale_id = $uf->_id ? $uf->_id : "";
    
    // - Soins
    $uf = new CUniteFonctionnelle();
    $uf->code = $results[$i]["uf_soins"];
    $uf->loadMatchingObject();
    $protocole->uf_soins_id = $uf->_id ? $uf->_id : "";
    
    if($protocole->libelle == "" || $protocole->duree_hospi === "" || $protocole->temp_operation == "") {
      $msg = "Champs manquants";
    }
    else {
      $msg = $protocole->store();
    }
    
    if ($msg) {
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
