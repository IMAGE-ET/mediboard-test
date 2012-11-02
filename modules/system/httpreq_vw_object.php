<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

// Look for view options
$options = CMbArray::filterPrefix($_GET, "view_");

$object->loadView();

// If no template is defined, use generic
$template = $object->makeTemplatePath("view");
$template = is_file("modules/$template") ?
   $template : 
  "system/templates/CMbObject_view.tpl";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display("../../$template");
?>