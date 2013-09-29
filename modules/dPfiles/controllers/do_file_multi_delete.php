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

$object_guid = CValue::post("object_guid");
$object = CMbObject::loadFromGuid($object_guid);

// Chargement de la ligne à rendre active
foreach ($object->loadBackRefs("files") as $_file) {
  $_POST["file_id"] = $_file->_id;
  $do = new CFileAddEdit;
  $do->redirect = null;
  $do->doIt();
}

echo CAppUI::getMsg();
CApp::rip();

