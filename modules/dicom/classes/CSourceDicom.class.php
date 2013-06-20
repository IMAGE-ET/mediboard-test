<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage DICOM
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The Dicom exchange source
 */
class CSourceDicom extends CExchangeSource {
  
  /**
   * Table Key
   * 
   * @var integer
   */
  public $source_dicom_id = null;
  
  /**
   * The port
   * 
   * @var integer
   */
  public $port = null;
  
  /**
   * The socket client to test the connection to the source
   * 
   * @var SocketClient
   */
  protected $_socket_client = null;
  
  /**
   * Initialize the class specifications
   * 
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "dicom_source";
    $spec->key    = "dicom_source_id";
    return $spec;
  }
  
  /**
   * Get the properties of our class as string
   * 
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["port"]  = "num notNull";
    return $props;
  }
  
  /**
   * Create the socket client
   * 
   * @return null
   */
  function setSocketClient() {
    $this->_socket_client = new SocketClient();
    $this->_socket_client->setHost($this->host);
    $this->_socket_client->setPort($this->port);
  }
  
  /**
   * Return a SocketClient
   * 
   * @return SocketClient
   */
  function getSocketClient() {
    if (!$this->_socket_client) {
      $this->setSocketClient();
    }
    return $this->_socket_client;
  }
  
  /**
   * Initiate a connection with the source and send an echo message
   * 
   * @return null
   */
  function sendEcho() {
    return;
  }
  
  /**
   * Check if the source is reachable
   *
   * @todo Faire un vrai isReachable (envoi de CEcho)
   *
   * @return boolean
   */
  function isReachableSource() {
    if (!$this->_socket_client) {
      $this->setSocketClient();
    }
    if ($this->_socket_client->connect() !== null) {
      $this->_reachable = 0;
      $this->_message   = CAppUI::tr("CSourceDicom-unreachable-source", $this->host);
      return false;
    }
    return true;
  }
}