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
$directory         = $source->getListDirectory($new_directory);

$tabRoot = explode("/", $current_directory);
array_pop($tabRoot);
$tabRoot[0] = "/";
$root = array();
$i =0;
foreach ($tabRoot as $_tabRoot) {

  if ($i === 0) {
    $path = "/";
  }
  else {
    $path = $root[count($root)-1]["path"]."$_tabRoot/";
  }
  $root[] = array("name" => $_tabRoot,
                  "path" => $path);
  $i++;
}

$smarty = new CSmartyDP();

$smarty->assign("current_directory", $current_directory);
$smarty->assign("root"             , $root);
$smarty->assign("directory"        , $directory);
$smarty->assign("source_guid"      , $source_guid);

$smarty->display("inc_manage_directory.tpl");
