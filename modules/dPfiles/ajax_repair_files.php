<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$nb_files = round(CValue::get("nb_files", 0));
$file = new CFile();

$file->file_size = 0;

$nb_files_size_zero = $file->countMatchingList();

if ($nb_files == 0) {
  CAppUI::stepAjax("Nombre de fichiers avec une taille de 0 octets : " . $nb_files_size_zero);  
}
else {
  $files = $file->loadMatchingList(null, $nb_files);
  
  if (count($files) == 0 ) {
    CAppUI::stepAjax("Aucun fichier à traiter");
  }
  else {
    foreach ($files as &$_file) {
      if (!file_exists($_file->_file_path) || filesize($_file->_file_path) === 0) {
        CAppUI::stepAjax("File id : " . $_file->_id . " - non existant ou vide - Suppression :" . $_file->delete());
      }
      else {
        $_file->file_size = filesize($_file->_file_path);
        CAppUI::stepAjax("File id : " . $_file->_id . " - mise à jour de la taille ({$_file->file_size} octets)- Update :" . $_file->store());
      }
    }
  }
}
?>