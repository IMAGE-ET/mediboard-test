<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

//$can->needsRead();

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

// Récupération des modèles
$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
$whereCommon[] = "`object_class` = '$selClass'";

$order = "nom";

// Modèles de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["chir_id"] = db_prepare("= %", $userSel->user_id);
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Modèles de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = db_prepare("= %", $userSel->function_id);
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}





// Création du template
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

$smarty->assign("selKey", $selKey);
$smarty->assign("listModeleFunc" , $listModeleFunc);
$smarty->assign("listModelePrat" , $listModelePrat);
$smarty->assign("reloadlist"     , $reloadlist  ); 
$smarty->assign("listCategory"   , $listCategory);
$smarty->assign("selClass"       , $selClass    );
$smarty->assign("selKey"         , $selKey      );
$smarty->assign("object"         , $object      );
$smarty->assign("typeVue"        , $typeVue     );
$smarty->assign("accordDossier"  , $accordDossier);

//$smarty->display("inc_list_view_colonne.tpl");
switch($typeVue) {
  case 0 :
    $smarty->display("inc_list_view.tpl");
    break;
  case 1 :
    $smarty->display("inc_list_view_colonne.tpl");
    break;
  case 2 :
    $smarty->display("inc_list_view_gd_thumb.tpl");
    break;
}


?>
