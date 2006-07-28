<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

$selClass = mbGetValueFromGetOrSession("selClass", null);
$keywords = mbGetValueFromGetOrSession("keywords", null);

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));


$keywords = trim($keywords);
$keywords_search = explode(" ", $keywords);
$keywords_search = array_filter($keywords_search);



if($selClass){
  $object = new $selClass;
  $list = $object->seek($keywords_search);
  foreach($list as $key => $value) {
    $list[$key]->loadRefsFwd();
  }
  $key = $object->_tbl_key;
}

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

if($selClass){
  $smarty->assign("key"        , $key);
  $smarty->assign("list"       , $list);
}
$smarty->assign("listClass"  , $listClass );
$smarty->assign("keywords"   , $keywords  );
$smarty->assign("selClass"   , $selClass  );

$smarty->display("object_selector.tpl");
?>
