<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m;
$ds = CSQLDataSource::get("std");
$can->needsRead();

$classes = array_flip(getInstalledClasses());
$listTraductions = array();

// Chargement des champs d'aides a la saisie
foreach ($classes as $class => &$infos) {  
  $listTraductions[$class] = CAppUI::tr($class);
  $object = new $class;
  $infos = array();
  foreach ($object->_specs as $field => $spec) {
   	if (isset($spec->helped)) {
   	  $info =& $infos[$field];
   	  $helped = $spec->helped;

   	  if (!is_array($helped)) {
   	    $info = null;
   	    continue;
   	  }
   	  
   	  foreach($helped as $i => $depend_field) {
   	    $key = "depend_value_" . ($i+1);
   	    $info[$key] = array();
   	    $list = &$info[$key];
   	    $list = array();
   	    foreach ($object->_specs[$depend_field]->_list as $value) {
   	      $locale = "$class.$depend_field.$value";
   	      $list[$value] = $locale;
   	      $listTraductions[$locale] = CAppUI::tr($locale);
   	    }
   	  }
   	}
  }
}

CMbArray::removeValue(array(), $classes);

// Liste des users accessibles
$listPrat = new CMediusers();
$listFct = $listPrat->loadFonctions(PERM_EDIT);

$where = array();
$where["users_mediboard.function_id"] = $ds->prepareIn(array_keys($listFct));

$ljoin = array();
$ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";

$order = "`users`.`user_last_name`, `users`.`user_first_name`";

$listPrat = $listPrat->loadList($where, $order, null, null, $ljoin);

foreach ($listPrat as $keyUser => $mediuser) {
  $mediuser->_ref_function =& $listFct[$mediuser->function_id];
}

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

$listEtab = CGroups::loadGroups(PERM_EDIT);

// Utilisateur sélectionné ou utilisateur courant
$filter_user_id = mbGetValueFromGetOrSession("filter_user_id");
$filter_class   = mbGetValueFromGetOrSession("filter_class");
$aide_id        = mbGetValueFromGetOrSession("aide_id");

$userSel = new CMediusers;
$userSel->load($filter_user_id ? $filter_user_id : $AppUI->user_id);
$userSel->loadRefs();
$userSel->_ref_function->loadRefGroup();

if ($userSel->isPraticien()) {
  mbSetValueToSession("filter_user_id", $userSel->user_id);
  $filter_user_id = $userSel->user_id;
}

$where = array();
if ($filter_class) {
  $where["class"] = "= '$filter_class'";
}

$order = array("group_id", "function_id", "user_id", "class", "depend_value_1", "field", "name");

// Liste des aides pour le praticien
$aidesPrat = array();
$aidesFunc = array();
$aidesEtab = array();

if ($userSel->user_id) {
  $userSel->loadRefFunction();
  $_aide = new CAideSaisie();
  
  $where = array("user_id" => "= '$userSel->user_id'");
  $aidesPrat = $_aide->loadlist($where, $order);
  foreach($aidesPrat as $aide) {
    $aide->loadRefsFwd();
  }

  $where = array("function_id" => "= '$userSel->function_id'");
  $aidesFunc = $_aide->loadlist($where, $order);
  foreach($aidesFunc as $aide) {
    $aide->loadRefsFwd();
  }

  $where = array("group_id" => "= '{$userSel->_ref_function->group_id}'");
  $aidesEtab = $_aide->loadlist($where, $order);
  foreach($aidesEtab as $aide) {
    $aide->loadRefsFwd();
  }
}

// Aide sélectionnée
$aide = new CAideSaisie();
$aide->load($aide_id); 
$aide->loadRefs();

if (!$aide_id) {
  $aide->user_id = $userSel->user_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("userSel"         , $userSel);
$smarty->assign("listPrat"        , $listPrat);
$smarty->assign("listFunc"        , $listFunc);
$smarty->assign("listEtab"        , $listEtab);
$smarty->assign("classes"         , $classes);
$smarty->assign("aide"            , $aide);
$smarty->assign("aidesPrat"       , $aidesPrat);
$smarty->assign("aidesFunc"       , $aidesFunc);
$smarty->assign("aidesEtab"       , $aidesEtab);
$smarty->assign("filter_class"    , $filter_class);
$smarty->assign("filter_user_id"  , $filter_user_id);
$smarty->assign("listTraductions" , $listTraductions);

$smarty->display("vw_idx_aides.tpl");
?>