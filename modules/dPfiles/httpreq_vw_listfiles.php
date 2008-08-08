<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

//$can->needsRead();
$ds = CSQLDataSource::get("std");
$selClass      = mbGetValueFromGetOrSession("selClass", null);
$selKey        = mbGetValueFromGetOrSession("selKey"  , null);
$typeVue       = mbGetValueFromGetOrSession("typeVue" , 0);
$accordDossier = mbGetValueFromGet("accordDossier"    , 0);
$reloadlist = 1;

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));
$listCategory = CFilesCategory::listCatClass($selClass);


// Id de l'utilisateur courant
$user_id = $AppUI->user_id;

// Chargement de l'utilisateur courant
$userSel = new CMediusers;
$userSel->load($user_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

$etablissements = CMediusers::loadEtablissements(PERM_EDIT);

// R�cup�ration des mod�les
$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
$whereCommon[] = "`object_class` = '$selClass'";

$order = "nom";

// Mod�les de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["chir_id"] = $ds->prepare("= %", $userSel->user_id);
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Mod�les de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = $ds->prepare("= %", $userSel->function_id);
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$object = null;

$canFile  = new CCanDo;

if($selClass && $selKey){
  // Chargement de l'objet
  $object = new $selClass;
  $object->load($selKey);
  $canFile = $object->canDo();
  
  $affichageFile = CFile::loadFilesAndDocsByObject($object);
  
  $smarty->assign("affichageFile",$affichageFile);
}

$smarty->assign("canFile"        , $canFile);

$smarty->assign("listModeleFunc" , $listModeleFunc);
$smarty->assign("listModelePrat" , $listModelePrat);
$smarty->assign("reloadlist"     , $reloadlist  ); 
$smarty->assign("listCategory"   , $listCategory);
$smarty->assign("selClass"       , $selClass    );
$smarty->assign("selKey"         , $selKey      );
$smarty->assign("object"         , $object      );
$smarty->assign("typeVue"        , $typeVue     );
$smarty->assign("accordDossier"  , $accordDossier);

switch($typeVue) {
  case 0 :
    $smarty->display("inc_list_view.tpl");
    break;
  case 1 :
    $smarty->display("inc_list_view_colonne.tpl");
    break;
}


?>
