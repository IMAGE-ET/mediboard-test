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

class CExchangeFTP extends CExchangeTransportLayer {
  // DB Table key
  public $echange_ftp_id;
  
  // DB Fields
  public $ftp_fault;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_ftp';
    $spec->key   = 'echange_ftp_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["ftp_fault"] = "bool";
    
    return $props;
  }
}