<?php

/**
 * Import users CSV
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$file   = CValue::files("import");
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
    $results[$i]["lastname"]        = CMbArray::get($line,  0);
    $results[$i]["firstname"]       = CMbArray::get($line,  1);
    $results[$i]["username"]        = CMbArray::get($line,  2);
    $results[$i]["password"]        = CMbArray::get($line,  3);
    $results[$i]["type"]            = CMbArray::get($line,  4);
    $results[$i]["function_name"]   = CMbArray::get($line,  5);
    $results[$i]["profil_name"]     = CMbArray::get($line,  6);
    if (CAppUI::conf("ref_pays") == 1) {
      $results[$i]["adeli"]         = CMbArray::get($line,  7);
      $results[$i]["rpps"]          = CMbArray::get($line,  8);
    }
    else {
      $results[$i]["ean"]           = CMbArray::get($line,  7);
      $results[$i]["rcc"]           = CMbArray::get($line,  8);
    }
    $results[$i]["spec_cpam_code"]  = CMbArray::get($line,  9);
    $results[$i]["discipline_name"] = CMbArray::get($line, 10);
    $results[$i]["idex"]            = CMbArray::get($line, 11);
    $results[$i]["remote"]          = CMbArray::get($line, 12);
    $results[$i]["error"]           = 0;

    if ($results[$i]["remote"] == "") {
      $results[$i]["remote"] = "1";
    }

    // User
    $mediuser = new CMediusers();
    $mediuser->_user_last_name  = $results[$i]["lastname"];
    $mediuser->_user_first_name = $results[$i]["firstname"];
    $mediuser->_user_type       = $results[$i]["type"];

    if (!is_numeric($mediuser->_user_type) || !array_key_exists($mediuser->_user_type, CUser::$types)) {
      $unfound["user_type"][$mediuser->_user_type] = true;
    }

    if (CAppUI::conf("ref_pays") == 1) {
      $mediuser->adeli            = $results[$i]["adeli"];
      $mediuser->rpps             = $results[$i]["rpps"];
    }
    else {
      $mediuser->ean              = $results[$i]["ean"];
      $mediuser->rcc              = $results[$i]["rcc"];
    }
    $mediuser->actif  = 1;
    $mediuser->remote = $results[$i]["remote"];

    // On force la regénération du mot de passe
    $mediuser->_force_change_password = true;

    // Password
    $mediuser->makeUsernamePassword($results[$i]["firstname"], $results[$i]["lastname"]);
    if ($results[$i]["password"]) {
      $mediuser->_user_password = $results[$i]["password"];
    }

    // Username
    if ($results[$i]["username"]) {
      $mediuser->_user_username = $results[$i]["username"];
    }

    $user = new CUser();
    $user->user_username = $mediuser->_user_username;
    if ($user->loadMatchingObject()) {
      $unfound["user"][$mediuser->_user_last_name] = true;
    }

    // Profil
    if ($profil_name = $results[$i]["profil_name"]) {
      $profil = new CUser();
      $profil->user_username = $profil_name;
      $profil->loadMatchingObject();
      if ($profil->_id) {
        $mediuser->_profile_id = $profil->_id;
      }
      else {
        $unfound["profil_name"][$profil_name] = true;
      }
    }
    $group_id = CGroups::loadCurrent()->_id;
    // Fonction
    $function = new CFunctions();
    $function->group_id = $group_id;
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
      $function->unescapeValues();
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

    $mediuser->function_id = $function->_id;
    
    // Spécialité CCAM
    if ($spec_cpam_code = $results[$i]["spec_cpam_code"]) {
      $spec_cpam = new CSpecCPAM();
      $spec_cpam->load(intval($spec_cpam_code));
      if ($spec_cpam->_id) {
        $mediuser->spec_cpam_id = $spec_cpam->_id;
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
        $mediuser->discipline_id = $discipline->_id;
      }
      else {
        $unfound["discipline_name"][$discipline_name] = true;
      }
    }

    // Dry run to check references
    if ($dryrun) {
      continue;
    }
    
    $mediuser->unescapeValues();
    $msg = $mediuser->store();
    if ($msg) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["error"] = $msg;
      $results[$i]["username"] = "";
      $results[$i]["password"] = "";
      continue;
    }
    CAppUI::setMsg("Utilisateur créé", UI_MSG_OK);
    $mediuser->insFunctionPermission();
    $mediuser->insGroupPermission();
    $results[$i]["result"] = 0;
    $results[$i]["username"] = $mediuser->_user_username;
    $results[$i]["password"] = $mediuser->_user_password;

    $number_idex = $results[$i]["idex"];
    if (!$number_idex) {
      continue;
    }
    $idex = new CIdSante400();
    $idex->tag = CMediusers::getTagMediusers($group_id);
    $idex->id400 = $number_idex;

    if ($idex->loadMatchingObject()) {
      $unfound["idex"][$number_idex] = true;
      CAppUI::setMsg("Identifiant déjà existant", UI_MSG_WARNING);
      continue;
    }
    $idex->setObject($mediuser);
    $msg = $idex->store();
    if ($msg) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
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
