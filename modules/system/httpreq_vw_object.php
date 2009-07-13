<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

// Look for view options
$options = CMbArray::filterPrefix($_GET, "view_");

$object->loadView();

$can->read = $object->canRead();
$can->edit = $object->canEdit();
$can->needsRead();

// If no template is defined, use generic
$template = is_file("modules/$object->_view_template") ?
  $object->_view_template : 
  "system/templates/CMbObject_view.tpl";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("show_views", "0");
$smarty->assign("show_derived", "0");
$smarty->assign("show_empty", "0");

$smarty->display("../../$template");
?>