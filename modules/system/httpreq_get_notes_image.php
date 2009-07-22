<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$object_class = mbGetValueFromGet("object_class");
$object_id    = mbGetValueFromGet("object_id");

if (!$object_class || !$object_id) {
  return;
}

$object = new $object_class;
$object->load($object_id);
$object->loadRefsNotes(PERM_READ);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object" , $object);
$smarty->assign("mode" , "edit");
$smarty->assign("float", "left");
$smarty->display("inc_get_notes_image.tpl");
?>