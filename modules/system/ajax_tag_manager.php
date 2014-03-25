<?php
/**
 * Created by PhpStorm.
 * User: charly
 * Date: 08/03/14
 * Time: 12:27
 */

CCanDo::checkRead();

$object_class   = CValue::get("object_class");

$tag = new CTag();
$tag->canDo();

// smarty
$smarty = new CSmartyDP();
$smarty->assign("tag", $tag);
$smarty->assign("object_class", $object_class);
$smarty->assign("limit", 15);
$smarty->display("inc_tag_manager.tpl");
