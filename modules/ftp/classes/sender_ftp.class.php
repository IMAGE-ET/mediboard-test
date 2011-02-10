<?php

/**
 * Interop Sender FTP
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSenderFTP 
 * Interoperability Sender FTP
 */
class CSenderFTP extends CInteropSender {
  // DB Table key
  var $sender_ftp_id  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sender_ftp';
    $spec->key   = 'sender_ftp_id';

    return $spec;
  }
  
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "ftp", true, $this->_type_echange);
  }
  
  function read() {}
}

?>