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

$source_guid       = CValue::get("source_guid");
$current_directory = CValue::get("current_directory");
$file              = CValue::read($_FILES, "import");

$source = CMbObject::loadFromGuid($source_guid);

//template
$smarty = new CSmartyDP();
$smarty->assign("source_guid"      , $source_guid);
$smarty->assign("current_directory", $current_directory);

if (!$file) {
  $smarty->display("inc_add_file.tpl");
  CApp::rip();
}

if ($source->addFile($file["tmp_name"], $file["name"], $current_directory)) {
  CAppUI::setMsg("Ajout du fichier");
}

$smarty->display("inc_add_file.tpl");