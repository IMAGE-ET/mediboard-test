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

$sender_ftp_id = CValue::get("sender_ftp_id");

$sender_ftp  = new CSenderFTP();
$senders_ftp = array();
if ($sender_ftp_id) {
  $sender_ftp->load($sender_ftp_id);
  $sender_ftp->loadRefsExchangesSources();
  $senders_ftp[] = $sender_ftp->actif ? $sender_ftp : array();
} else {
  // Chargement de la liste des expéditeurs d'intégration
  $where = array();
  $where["actif"] = " = '1'";
  $senders_ftp = $sender_ftp->loadList($where);
  foreach ($senders_ftp as $_sender_ftp) {
    $_sender_ftp->loadRefsExchangesSources();
  }
}

foreach ($senders_ftp as $_sender_ftp) {
  echo CApp::fetch("ftp", "ajax_dispatch_files", array("sender_ftp_guid" => $_sender_ftp->_guid));
}


