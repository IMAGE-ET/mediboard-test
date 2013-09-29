<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$path = CValue::post("path");
$object_class = CValue::post("object_class");
$object_id = CValue::post("object_id");

$file = new CFile;
$file->object_class = $object_class;
$file->object_id    = $object_id;
$file->author_id   = CAppUI::$user->_id;
$file->file_name    = basename($path);
$file->fillFields();
$file->forcerDir();

if ($msg = $file->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR ); return;
}

$file->moveTemp($path);
