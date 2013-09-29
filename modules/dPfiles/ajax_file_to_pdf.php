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

$nb_files = CValue::get("nb_files", 20);
$extensions = CValue::get("extensions", CFile::$file_types);
$file = new CFile();
$where = array();
$where["object_class"] = " NOT LIKE 'CFile'";

// Ne convertir que les fichiers dont le nom se finit par une extension convertible
$like = "";
$types = preg_split("/[\s]+/", $extensions);

foreach ($types as $key => $_type) {
  $like .= " file_name LIKE '%.$_type'";
  if ($key != (count($types) -1)) {
    $like .= " OR";
  }
}
$where[] = $like;
$where[] = "file_id NOT IN (SELECT object_id from files_mediboard WHERE object_class LIKE 'CFile')";

$order = "file_id DESC";

$files = $file->loadList($where, $order, $nb_files);

$nb_files_total = $file->countList($where);

$converted = 0;
$not_converted = "";

foreach ($files as $_file) {
  if ($_file->convertToPDF()) {
    $converted ++;
  }
  else {
    $not_converted .= $_file->_id . " - ";
  }
}

CAppUI::stepAjax("$converted/".count($files) . " fichiers convertis parmi $nb_files_total");

if ($converted != count($files)) {
  trigger_error("Les fichiers suivants n'ont pas été convertis : $not_converted", E_USER_ERROR);
}

