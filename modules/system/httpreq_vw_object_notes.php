<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

$object->loadRefsNotes(PERM_READ);

foreach ($object->_ref_notes as $note) {
  $note->_ref_user->loadRefsFwd();
  $note->_date_relative = CMbDate::relative($note->date);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("notes", $object->_ref_notes);
$smarty->assign("object", $object);
$smarty->display("vw_object_notes.tpl");
