<?php /* $Id: prat_import_csv.php 6103 2009-04-16 13:36:52Z yohann $ */

/**
 * @package Mediboard
 * @subpackage mediusers
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
  while($line = fgetcsv($fp, null, ";")) {
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }
    
    // Parsing
    $results[$i]["lastname"]      = addslashes(trim($line[0]));
    $results[$i]["firstname"]     = addslashes(trim($line[1]));
    $results[$i]["login"]         = addslashes(trim($line[2]));
    $results[$i]["password"]      = addslashes(trim($line[3]));
    $results[$i]["type"]          = addslashes(trim($line[4]));
    $results[$i]["function_name"] = addslashes(trim($line[5]));
    $results[$i]["profil_name"]   = addslashes(trim($line[6]));
    
    $results[$i]["error"] = 0;
    
    // Fonction
    $function = new CFunctions();
    $function->group_id = CGroups::loadCurrent()->_id;
    $function->text     = $results[$i]["function_name"];
    $function->loadMatchingObject();
    if (!$function->_id) {
      if(in_array($results[$i]["type"], array("3", "4", "13"))) {
        $function->type = "cabinet";
      }
      else {
        $function->type = "administratif";
      }
      $function->color              = "ffffff";
      $function->compta_partagee    = 0;
      $function->consults_partagees = 1;
      $msg = $function->store();
      if ($msg) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
        $results[$i]["error"] = $msg;
        $results[$i]["username"] = "";
        $results[$i]["password"] = "";
        $i++;
        continue;
      }
    }
    
    // Profil
    // @TODO : relier un profil
    $profil = new CUser();
    $profil->user_username = $results[$i]["profil_name"];
    $profil->loadMatchingObject();
    
    
    // User
    $user = new CMediusers();
    $user->_user_last_name  = $results[$i]["lastname"];
    $user->_user_first_name = $results[$i]["firstname"];
    $user->_user_type       = $results[$i]["type"];
    if ($profil->_id) {
      $user->_profile_id       = $profil->_id;
    }
    else {
      $results[$i]["profil_name"] .= " : Non trouvé";
    }
    $user->makeUsernamePassword($results[$i]["firstname"], $results[$i]["lastname"]);
    if ($results[$i]["login"]) {
      $user->_user_username = $results[$i]["login"];
    }
    if ($results[$i]["password"]) {
      $user->_user_password = $results[$i]["password"];
    }
    $user->actif  = 1;
    $user->remote = 0;
    $user->function_id = $function->_id;
    
    $msg = $user->store();
    if ($msg) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["error"] = $msg;
      $results[$i]["username"] = "";
      $results[$i]["password"] = "";
      $i++;
      continue;
    }
    CAppUI::setMsg("Utilisateur créé", UI_MSG_OK);
    $user->insFunctionPermission();
    $user->insGroupPermission();
    $results[$i]["result"] = 0;
    $results[$i]["username"] = $user->_user_username;
    $results[$i]["password"] = $user->_user_password;
    
    $i++;
  }
  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("user_import_csv.tpl");
