<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_guid = CValue::get("object_guid");

if (!$object_guid) return;

$object = CMbObject::loadFromGuid($object_guid);
$object->loadRefsNotes(PERM_READ);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("mode" , "edit");
$smarty->assign("float", "left");
$smarty->display("inc_get_notes_image.tpl");
