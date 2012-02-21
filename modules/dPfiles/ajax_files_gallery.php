<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPfiles
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

$object = new $object_class;
$object->load($object_id);

$files = CFile::loadDocItemsByObject($object);

foreach ($files as $_files_by_cat) {
  foreach ($_files_by_cat["items"] as $_file) {
    if ($_file instanceof CCompteRendu) {
      $_file->makePDFpreview();
    }
  }
}
$smarty = new CSmartyDP;

$smarty->assign("object", $object);
$smarty->assign("files", $files);

$smarty->display("inc_files_gallery.tpl");

?>