<?php 
/**
 * Read FTP files
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_source_name = CValue::get("exchange_source_name");

$exchanges_source = array();
if ($exchange_source_name) {
  $exchanges_source[] = CExchangeSource::get($exchange_source_name);
} else {
  // Chargement de la liste des expéditeurs d'intégration
  
}

foreach ($exchanges_source as $_exchange_source) {
  $extension = $_exchange_source->fileextension;
  
  $list = array();
  
  $ftp = new CFTP();
  $ftp->init($_exchange_source);
  try {
    $ftp->connect();
    $list = $ftp->getListFiles("./");
  } catch (CMbException $e) {
    $e->stepAjax();
  }
  
  if (empty($list)) {
    CAppUI::stepAjax("Le répertoire ne contient aucun fichier", UI_MSG_ERROR);
  }
  
  foreach($list as $filepath) {
    if (substr($filepath, -(strlen($extension))) == $extension) {
      $filename = basename($filepath);
      
    } else {
      
    }
  }
}


?>