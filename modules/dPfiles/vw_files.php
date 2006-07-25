<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPfiles", "filescategory"));
require_once($AppUI->getModuleClass("dPfiles", "files"        ));

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$selClass = mbGetValueFromGetOrSession("selClass", null);
$keywords = mbGetValueFromGetOrSession("keywords", null);
$selKey   = mbGetValueFromGetOrSession("selKey"  , null);
$selView  = mbGetValueFromGetOrSession("selView" , null);

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));

$listCategory = CFilesCategory::listCatClass($selClass);


// Cr�ation du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$object = null;

if($selClass && $selKey){
  $object = new $selClass;
  $object->load($selKey);
  $object->loadRefsFiles();
}

mbTrace($object->_ref_files);
$smarty->assign("listCategory", $listCategory);
$smarty->assign("listClass"   , $listClass   );
$smarty->assign("selClass"    , $selClass    );
$smarty->assign("selKey"      , $selKey      );
$smarty->assign("selView"     , $selView     );
$smarty->assign("keywords"    , $keywords    );
$smarty->assign("object"      , $object      );

$smarty->display("vw_files.tpl");

?>
