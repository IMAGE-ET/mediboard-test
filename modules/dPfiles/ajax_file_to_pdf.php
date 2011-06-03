<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$nb_files = CValue::get("nb_files", 20);
$extensions = CValue::get("extensions", CFile::$file_types);
$file = new CFile();
$where = array();
$where["object_class"] = " NOT LIKE 'CFile'";

// Ne convertir que les fichiers dont le nom se finit par une extension convertible
$rlike = "";
$types = preg_split("/[\s]+/", $extensions);

foreach($types as $key => $_type) {
  $rlike .= " file_name RLIKE '.".$_type."$'";
  if ($key != (count($types) -1)) {
    $rlike .= " OR";
  }
}
$where[] = $rlike;
$where[] = "file_id NOT IN (SELECT object_id from files_mediboard WHERE object_class LIKE 'CFile')";

$order = "file_id DESC";

$files = $file->loadList($where, $order, $nb_files);

$converted = 0;
$not_converted = "";

foreach($files as $_file) {
  if ($_file->convertToPDF()) {
    $converted ++;
  }
  else {
    $not_converted .= $_file->_id . " - ";
  }
}

if ($converted != count($files)) {
  trigger_error("Les fichiers suivants n'ont pas été convertis : $not_converted", E_USER_ERROR);
}

?>