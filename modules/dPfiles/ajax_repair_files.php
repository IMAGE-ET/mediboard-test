<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$nb_files   = round(CValue::get("nb_files", 0));
$date_debut = CValue::get("date_debut");
$date_fin   = CValue::get("date_fin");
$purge      = CValue::get("purge", 0);
$step_from  = CValue::get("step_from", 0);
$file = new CFile;

if ($date_debut && $date_fin) {
  $where = array();
  $where["file_date"] =  "BETWEEN '".CMbDT::dateTime($date_debut)."' AND '".CMbDT::dateTime($date_fin)."'";
  
  $files = $file->loadList($where, null, "$step_from, 100");
  $count = 0;
  
  foreach ($files as $_file) {
    if (!file_exists($_file->_file_path) || filesize($_file->_file_path) == 0 || file_get_contents($_file->_file_path) == "") {
      if (!$purge) {
        CAppUI::stepAjax($_file->_id);
        $count ++; continue;
      }
      if ($msg = $_file->purge()) {
        CAppUI::stepAjax("File id: " . $_file->_id . " - " . $_file->purge());
      }
      else {
        $count++;
      }
    }
  }
  if ($purge) {
    CAppUI::stepAjax("$count fichiers supprimés");
  }
  else {
    CAppUI::stepAjax("$count fichiers à traiter");
  }
}
else {
  $file->file_size = 0;
  $nb_files_size_zero = $file->countMatchingList();
  if ($nb_files == 0) {
    CAppUI::stepAjax("Nombre de fichiers avec une taille de 0 octets : " . $nb_files_size_zero);  
  }
  else {
    $where = array();
    $where["file_size"] = " = '0'";
    $files = $file->loadList($where, null, $nb_files);
    
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
}
?>