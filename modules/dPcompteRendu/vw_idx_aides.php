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

// Liste des Class
$listClass       = getInstalledClasses();
$listTraductions = array();
$classes         = array();

// Chargement des champs d'aides a la saisie
foreach($listClass as $sClassName){  
  $object = new $sClassName;
  if(count($object->_helped_fields)){
    $listTraductions[$sClassName] = CAppUI::tr($sClassName);
    
    foreach($object->_helped_fields as $field =>$help_field){
      $classes[$sClassName][$field] = null;
      if(is_array($help_field)){
      foreach($help_field as $num_depend => $_depend_value){
	      
	      if($_depend_value){
	        $specType = $object->_specs[$_depend_value]->getSpecType();
	        if($specType == "enum"){
	          $entryEnums = array();
	          // Ecriture du tableau de traduction
	          foreach($object->_enumsTrans[$_depend_value] as $key => $sTraduction){
	            $listTraductions["$sClassName.$_depend_value.$key"] = $sTraduction;
	            $entryEnums[$key] = "$sClassName.$_depend_value.$key";
	          }
	          $classes[$sClassName][$field][$num_depend] = $entryEnums;
	        }
	      }
      }
      }
    }
  }
}

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


// Utilisateur sélectionné ou utilisateur courant
$filter_user_id = mbGetValueFromGetOrSession("filter_user_id");
$filter_class   = mbGetValueFromGetOrSession("filter_class");
$aide_id        = mbGetValueFromGetOrSession("aide_id");

$userSel = new CMediusers;
$userSel->load($filter_user_id ? $filter_user_id : $AppUI->user_id);
$userSel->loadRefs();

if ($userSel->isPraticien()) {
  mbSetValueToSession("filter_user_id", $userSel->user_id);
  $filter_user_id = $userSel->user_id;
}

$where = array();
if ($filter_class) {
  $where["class"] = "= '$filter_class'";
}

$order = array("user_id", "class", "depend_value_1", "field", "name");

// Liste des aides pour le praticien
$aidesPrat = array();
if ($userSel->user_id) {
  $where["user_id"] = "= '$userSel->user_id'";
  // $where["user_id"] = "= '$filter_user_id'";
  $aidesPrat = new CAideSaisie();
  $aidesPrat = $aidesPrat->loadlist($where, $order);
  foreach($aidesPrat as $key => $aide) {
    $aidesPrat[$key]->loadRefsFwd();
  }
  unset($where["user_id"]);
}

// Liste des aides pour la fonction du praticien
$aidesFunc = array();
if ($userSel->user_id) {
  $where["function_id"] = "= '$userSel->function_id'";
  $aidesFunc = new CAideSaisie();
  $aidesFunc = $aidesFunc->loadlist($where, $order);
  foreach($aidesFunc as $key => $aide) {
    $aidesFunc[$key]->loadRefsFwd();
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
$smarty->assign("classes"         , $classes);
$smarty->assign("aide"            , $aide);
$smarty->assign("aidesFunc"       , $aidesFunc);
$smarty->assign("aidesPrat"       , $aidesPrat);
$smarty->assign("filter_class"    , $filter_class);
$smarty->assign("filter_user_id"  , $filter_user_id);
$smarty->assign("listTraductions" , $listTraductions);

$smarty->display("vw_idx_aides.tpl");
?>