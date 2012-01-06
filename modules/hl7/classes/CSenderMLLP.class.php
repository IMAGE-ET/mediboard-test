<?php

/**
 * Interop Sender MLLP
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSenderMLLP 
 * Interoperability Sender MLLP
 */
class CSenderMLLP extends CInteropSender {
  // DB Table key
  var $sender_mllp_id  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sender_mllp';
    $spec->key   = 'sender_mllp_id';

    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["expediteur_ihe"]      = "CExchangeIHE sender_id";
    return $backProps;
  }
  
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "mllp", true, $this->_type_echange);
  }
  
  function read() {
    $this->loadRefsExchangesSources();
  }
}

?>