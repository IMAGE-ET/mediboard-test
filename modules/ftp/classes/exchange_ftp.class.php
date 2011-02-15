<?php

/**
 * Exchange FTP
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("system", "exchange_transport_layer");

class CExchangeFTP extends CExchangeTransportLayer {
  // DB Table key
  var $echange_ftp_id = null;
  
  // DB Fields
  var $ftp_fault      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_ftp';
    $spec->key   = 'echange_ftp_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["ftp_fault"] = "bool";
    
    return $props;
  }
}
?>