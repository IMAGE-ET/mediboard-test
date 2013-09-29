<?php
/**
 * Envoi de fichiers par yoplet
 *
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$path = CAppUI::conf("dPfiles yoplet_upload_path");

if (!$path) {
  $path = "tmp";
}

$file_name = CValue::post("checksum");
file_put_contents("$path/".$file_name, file_get_contents($_FILES["file"]["tmp_name"]));
