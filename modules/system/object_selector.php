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
$listClass = getChildClasses();

$keywords = trim($keywords);
$keywords = explode(" ", $keywords);
$keywords = array_filter($keywords);

$object = new $selClass;
$list = $object->seek($keywords);
foreach($list as $key => $value) {
  $list[$key]->loadRefsFwd();
}

$key = $object->_tbl_key;

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("key"  , $key);
$smarty->assign("list" , $list);

$smarty->display("object_selector.tpl");
?>
