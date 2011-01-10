<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

$object = new $object_class;
$object->load($object_id);

$object->loadRefsDocs();
  
$smarty = new CSmartyDP();

$smarty->assign("documents", $object->_ref_documents);
$smarty->display("print_select_docs.tpl");

?>