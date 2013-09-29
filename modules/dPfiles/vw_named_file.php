<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$object = mbGetObjectFromGetOrSession("object_class", "object_id", "object_guid");
$name   = CValue::get("name");
$size   = CValue::get("size");
$mode   = CValue::get("mode", "edit");

$object->loadNamedFile($name);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("name"  , $name);
$smarty->assign("size"  , $size);
$smarty->assign("mode"  , $mode);

$smarty->display("inc_named_file.tpl");
