<?php /* $Id: prat_import_csv.php 6103 2009-04-16 13:36:52Z yohann $ */

/**
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision: 6153 $
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
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }
    
    // Parsing
    $line = array_map("trim"      , $line);
    $line = array_map("addslashes", $line);
    $results[$i]["lastname"]        = CMbArray::get($line,  0);
    $results[$i]["firstname"]       = CMbArray::get($line,  1);
    $results[$i]["username"]        = CMbArray::get($line,  2);
    $results[$i]["password"]        = CMbArray::get($line,  3);
    $results[$i]["type"]            = CMbArray::get($line,  4);
    $results[$i]["function_name"]   = CMbArray::get($line,  5);
    $results[$i]["profil_name"]     = CMbArray::get($line,  6);
    $results[$i]["adeli"]           = CMbArray::get($line,  7);
    $results[$i]["rpps"]            = CMbArray::get($line,  8);
    $results[$i]["spec_cpam_code"]  = CMbArray::get($line,  9);
    $results[$i]["discipline_name"] = CMbArray::get($line, 10);
    
    $results[$i]["error"] = 0;
        
    // User
    $user = new CMediusers();
    $user->_user_last_name  = $results[$i]["lastname"];
    $user->_user_first_name = $results[$i]["firstname"];
    $user->_user_type       = $results[$i]["type"];
    $user->adeli            = $results[$i]["adeli"];
    $user->rpps             = $results[$i]["rpps"];
    $user->actif  = 1;
    $user->remote = 0;
    
    // Username
    if ($results[$i]["username"]) {
      $user->_user_username = $results[$i]["username"];
    }
    
    // Password
    $user->makeUsernamePassword($results[$i]["firstname"], $results[$i]["lastname"]);
    if ($results[$i]["password"]) {
      $user->_user_password = $results[$i]["password"];
    }

    // Profil
    if ($profil_name = $results[$i]["profil_name"]) {
      $profil = new CUser();
      $profil->user_username = $profil_name;
      $profil->loadMatchingObject();
      if ($profil->_id) {
        $user->_profile_id = $profil->_id;
      }
      else {
        $unfound["profil_name"][$profil_name] = true;
      }
    }

    // Fonction
    $function = new CFunctions();
    $function->group_id = CGroups::loadCurrent()->_id;
    $function->text     = $results[$i]["function_name"];
    $function->loadMatchingObject();
    if (!$function->_id) {
      if (in_array($results[$i]["type"], array("3", "4", "13"))) {
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

    $user->function_id = $function->_id;
    
    // Spécialité CCAM
    if ($spec_cpam_code = $results[$i]["spec_cpam_code"]) {
      $spec_cpam = new CSpecCPAM();
      $spec_cpam->load(intval($spec_cpam_code));
      if ($spec_cpam->_id) {
        $user->spec_cpam_id = $spec_cpam->_id;
      }
      else {
        $unfound["spec_cpam_code"][$spec_cpam_code] = true;
      }
    }
    
    // Discipline
    if ($discipline_name = $results[$i]["discipline_name"]) {
      $discipline = new CDiscipline();
      $discipline->text = strtoupper($discipline_name);
      $discipline->loadMatchingObject();
      if ($discipline->_id) {
        $user->discipline_id = $discipline->_id;
      }
      else {
        $unfound["discipline_name"][$discipline_name] = true;
      }
    }
    
    // Dry run to check references
    if ($dryrun) {
      continue;
    }
    
    $msg = $user->store();
    if ($msg) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["error"] = $msg;
      $results[$i]["username"] = "";
      $results[$i]["password"] = "";
      continue;
    }
    CAppUI::setMsg("Utilisateur créé", UI_MSG_OK);
    $user->insFunctionPermission();
    $user->insGroupPermission();
    $results[$i]["result"] = 0;
    $results[$i]["username"] = $user->_user_username;
    $results[$i]["password"] = $user->_user_password;
  }
  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dryrun", $dryrun);
$smarty->assign("results", $results);
$smarty->assign("unfound", $unfound);

$smarty->display("user_import_csv.tpl");
