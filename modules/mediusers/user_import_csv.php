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
    
    $results[$i]["lastname"]      = trim(CMbString::removeDiacritics($line[0]));
    $results[$i]["firstname"]     = trim(CMbString::removeDiacritics($line[1]));
    $results[$i]["type"]          = trim($line[2]);
    $results[$i]["function_name"] = trim(ucfirst(strtolower(CMbString::removeDiacritics($line[3]))));
    $results[$i]["profil_name"]   = trim(ucfirst(strtolower(CMbString::removeDiacritics($line[4]))));
    
    $user = new CMediusers();
    $user->_user_last_name  = $results[$i]["lastname"];
    $user->_user_first_name = $results[$i]["firstname"];
    $user->_user_type       = $results[$i]["type"];
    $user->makeUsernamePassword($results[$i]["firstname"], $results[$i]["lastname"]);
    $user->actif  = 1;
    $user->remote = 0;
    
    // Fonction
    $function = new CFunctions();
    $function->group_id = CGroups::loadCurrent()->_id;
    $function->text     = $results[$i]["function_name"];
    $function->loadMatchingObject();
    if(!$function->_id) {
      $function->type               = "administratif";
      $function->color              = "ffffff";
      $function->compta_partagee    = 0;
      $function->consults_partagees = 1;
      $msg = $function->store();
      if($msg) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
    $user->function_id = $function->_id;
    
    // Profil
    // @TODO : relier un profil
    
    $msg = $user->store();
    if($msg) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["username"] = "Utilisateur déjà existant";
      $results[$i]["password"] = "Utilisateur déjà existant";
    } else {
      CAppUI::setMsg("Utilisateur créé", UI_MSG_OK);
      $results[$i]["username"] = $user->_user_username;
      $results[$i]["password"] = $user->_user_password;
    }
    $i++;
  }
  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("user_import_csv.tpl");
