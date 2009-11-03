<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_class = CValue::get("object_class");
$object_id    = CValue::get("object_id");

if (!$object_class || !$object_id) return;

$object = new $object_class;
$object->load($object_id);
$object->loadRefsNotes(PERM_READ);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object" , $object);
$smarty->assign("mode" , "edit");
$smarty->assign("float", "left");
$smarty->display("inc_get_notes_image.tpl");
