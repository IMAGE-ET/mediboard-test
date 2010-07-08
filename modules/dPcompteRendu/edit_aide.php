<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can;
$can->needsRead();
$choicepratcab = CAppUI::pref("choicepratcab");
$aide_id       = CValue::get("aide_id", CValue::post("aide_id", ''));
$class         = CValue::get("class");
$field         = CValue::get("field");
$text          = utf8_decode(CValue::get("text", CValue::post("text", "")));
$depend_value_1 = CValue::get("depend_value_1");
$depend_value_2 = CValue::get("depend_value_2");

// Liste des users accessibles
$listPrat = new CMediusers();
$listFct = $listPrat->loadFonctions(PERM_EDIT);
$listPrat = $listPrat->loadUsers(PERM_EDIT);

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

$listEtab = CGroups::loadGroups(PERM_EDIT);

// Objet ciblé
$object = new $class;
$dependValues = array();

// To set the depend values always as an array (empty or not)
$helped = array();
if ($object->_specs[$field]->helped && !is_bool($object->_specs[$field]->helped)) {
	if (!is_array($object->_specs[$field]->helped)) 
	  $helped = array($object->_specs[$field]->helped);
	else 
	  $helped = $object->_specs[$field]->helped;
}
foreach ($helped as $i => $depend_field) {
  $key = "depend_value_" . ($i+1);
  $dependValues[$key] = @$object->_specs[$depend_field]->_locales;
}

// Liste des aides
$user_id = CValue::get("user_id", CAppUI::$user->_id);
if (!$user_id) {
  $user_id = CAppUI::$user->_id;
}

$user = new CMediusers();
$user->load($user_id);
$user->loadRefFunction();

$group = CGroups::loadCurrent();

$aidebis = new CAideSaisie();
$where[] = "`field` = '".$field."' AND (
  user_id     = " . $user_id . " OR 
  function_id = " . $user->function_id . " OR 
  group_id    = " . $group->_id . "
)";

$orderby = "name";
$aides = $aidebis->loadList($where, $orderby);

$aide = new CAideSaisie();
if($aide_id) {
  // Chargement de l'aide
  $aide->load($aide_id);
} else {
  // Nouvelle Aide à la saisie
  $aide->class        = $class;
  $aide->field        = $field;
  $aide->text         = stripslashes($text);
  $aide->depend_value_1 = $depend_value_1;
  $aide->depend_value_2 = $depend_value_2;
  //switch(CAppUI::pref("choicepratcab")) {
    /*case "prat":*/  $aide->user_id = $user_id; //break;
    /*case "cab":*/   $aide->function_id = CAppUI::$user->function_id; //break;
    /*case "group":*/ $aide->group_id = $group->_id;
  //}
}

$fields = array(
    "user_id" => $user_id,
    "function_id" => $user->function_id,
    "group_id" => $group->_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("aide"     , $aide);
$smarty->assign("aide_id"  , $aide_id);
$smarty->assign("dependValues", $dependValues);
$smarty->assign("listFunc" , $listFunc);
$smarty->assign("listPrat" , $listPrat);
$smarty->assign("listEtab" , $listEtab);
$smarty->assign("aides"    , $aides);
$smarty->assign("user"    ,  $user);
$smarty->assign("group"    , $group);
$smarty->assign("choicepratcab", $choicepratcab);
$smarty->assign("fields", $fields);
$smarty->display("vw_edit_aides.tpl");
?>