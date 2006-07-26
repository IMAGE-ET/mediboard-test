<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPfiles", "filescategory"));
require_once($AppUI->getModuleClass("dPfiles", "files"        ));

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$selClass = mbGetValueFromGetOrSession("selClass", null);
$selKey   = mbGetValueFromGetOrSession("selKey"  , null);


// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));

$listCategory = CFilesCategory::listCatClass($selClass);

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$object = null;

if($selClass && $selKey){
  $object = new $selClass;
  $object->load($selKey);
  $object->loadRefsFiles();
}

$smarty->assign("listCategory", $listCategory);
$smarty->assign("selClass"    , $selClass    );
$smarty->assign("selKey"      , $selKey      );
$smarty->assign("object"      , $object      );

$smarty->display("inc_list_view.tpl");

?>
