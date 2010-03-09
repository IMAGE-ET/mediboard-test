<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfiles
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

//$can->needsRead();

$object_class  = CValue::getOrSession("selClass", null);
$object_id     = CValue::getOrSession("selKey"  , null);
$typeVue       = CValue::getOrSession("typeVue" , 0);
$accordDossier = CValue::get("accordDossier"    , 0);
$reloadlist = 1;

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));
$listCategory = CFilesCategory::listCatClass($object_class);

// Id de l'utilisateur courant
$user_id = $AppUI->user_id;

// Chargement de l'utilisateur courant
$mediuser = new CMediusers;
$mediuser->load($user_id);
$mediuser->loadRefs();

$object = null;
$canFile  = new CCanDo;
$praticienId = null;
$affichageFile = array();

if($object_id && $object_class){
  // Chargement de l'objet
  $object = new $object_class;
  $object->load($object_id);
  $canFile = $object->canDo();
  
  // To add the modele selector in the toolbar
  if ($object_class == 'CConsultation') {
    $praticienId = $object->_praticien_id;
  } 
  else if ($object_class == 'CConsultAnesth') {
    $praticienId = $object->_ref_consultation->_praticien_id;
  }
  else if ($object_class == 'CSejour') {
    $praticienId = $object->praticien_id;
  }
  else if ($object_class == 'COperation') {
    $praticienId = $object->chir_id;
  }
  else if ($mediuser->isPraticien()) {
    $praticienId = $mediuser->_id;
  }
  
  $affichageFile = CFile::loadDocItemsByObject($object);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affichageFile", $affichageFile);
$smarty->assign("canFile"      , $canFile);
$smarty->assign("reloadlist"   , $reloadlist); 
$smarty->assign("listCategory" , $listCategory);
$smarty->assign("praticienId"  , $praticienId);
$smarty->assign("selClass"     , $object_class);
$smarty->assign("selKey"       , $object_id);
$smarty->assign("object"       , $object);
$smarty->assign("typeVue"      , $typeVue);
$smarty->assign("accordDossier", $accordDossier);

switch($typeVue) {
  case 0 :
    $smarty->display("inc_list_view.tpl");
    break;
  case 1 :
    $smarty->display("inc_list_view_colonne.tpl");
    break;
}

?>