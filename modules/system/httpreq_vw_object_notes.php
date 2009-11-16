<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

$object->loadRefsNotes(PERM_READ);

foreach ($object->_ref_notes as $note) {
  $note->_ref_user->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("notes", $object->_ref_notes);
$smarty->display("vw_object_notes.tpl");
?>