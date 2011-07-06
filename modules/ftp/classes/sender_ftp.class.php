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
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["expediteur_hprimxml"] = "CEchangeHprim sender_id";
    $backProps["expediteur_hprim21"]  = "CEchangeHprim21 sender_id";
    $backProps["config_hprimxml"]     = "CHprimXMLConfig sender_id";
    $backProps["expediteur_phast"]    = "CPhastEchange sender_id";
    return $backProps;
  }
  
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "ftp", true, $this->_type_echange);
  }
  
  function read() {
    $this->loadRefsExchangesSources();
    
    
  }
}

?>