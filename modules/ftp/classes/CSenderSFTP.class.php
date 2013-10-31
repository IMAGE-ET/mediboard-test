<?php

/**
 * $Id$
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Class CSenderSFTP
 * Interoperability Sender SFTP
 */
class CSenderSFTP extends CInteropSender {
  /**
   * @var integer Primary key
   */
  public $sender_sftp_id;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "sender_sftp";
    $spec->key    = "sender_sftp_id";
    return $spec;  
  }

  /**
   * @see parent::loadRefsExchangesSources
   */
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "sftp", true, $this->_type_echange, false);
  }

  /**
   * @see parent::read
   */
  function read() {
  }
}