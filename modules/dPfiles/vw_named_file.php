<?php /* $Id: upload_file.php 15530 2012-05-15 12:15:45Z mytto $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: 15530 $
* @author Sébastien Fillonneau
*/

CCanDo::checkRead();

$object = mbGetObjectFromGetOrSession("object_class", "object_id", "object_guid");
$name   = CValue::get("name");
$mode   = CValue::get("mode", "edit");

$object->loadNamedFile($name);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("name"  , $name);
$smarty->assign("mode"  , $mode);

$smarty->display("inc_named_file.tpl");
?>