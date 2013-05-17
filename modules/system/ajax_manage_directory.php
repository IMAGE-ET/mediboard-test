<?php

/**
 * $Id$
 *
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */


CCanDo::checkAdmin();

$new_directory = CValue::get("new_directory");
$source_guid   = CValue::get("source_guid");

$source = CMbObject::loadFromGuid($source_guid);

$current_directory = $source->getCurrentDirectory($new_directory);
$directory         = $source->getListDirectory($current_directory);
$root              = $source->getRootDirectory($current_directory);


$smarty = new CSmartyDP();

$smarty->assign("current_directory", $current_directory);
$smarty->assign("root"             , $root);
$smarty->assign("directory"        , $directory);
$smarty->assign("source_guid"      , $source_guid);

$smarty->display("inc_manage_directory.tpl");
