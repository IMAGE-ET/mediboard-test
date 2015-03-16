<?php

/**
 * $Id: CSocketSource.class.php 21579 2014-01-06 10:25:35Z kgrisel $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 21579 $
 */

/**
 * Class CSocketSource
 */
class CSocketSource extends CExchangeSource {
  public $protocol;
  public $ssl_certificate;
  public $ssl_passphrase;
  public $port;
  public $timeout;

  public $_socket_client;

  /**
   * Check if source is secured
   *
   * @return bool
   */
  function isSecured() {
  }

  /**
   * Get transport protocol to use
   *
   * @return string
   */
  function getProtocol() {
  }

  /**
   * Get socket client
   *
   * @return SocketClient
   * @throws CMbException
   */
  function getSocketClient() {
    if ($this->_socket_client) {
      return $this->_socket_client;
    }

    $address = $this->getProtocol() . "://$this->host:$this->port";
    $context = stream_context_create();

    if ($this->isSecured()) {
      stream_context_set_option($context, 'ssl', 'local_cert', $this->ssl_certificate);

      if ($this->ssl_passphrase) {
        $ssl_passphrase = $this->getPassword($this->ssl_passphrase, "iv_passphrase");
        stream_context_set_option($context, 'ssl', 'passphrase', $ssl_passphrase);
      }
    }

    $this->_socket_client = @stream_socket_client($address, $errno, $errstr, ($this->timeout) ? $this->timeout : 5, STREAM_CLIENT_CONNECT, $context);
    if (!$this->_socket_client) {
      throw new CMbException("common-error-Unreachable source", $this->name);
    }

    stream_set_blocking($this->_socket_client, 0);

    return $this->_socket_client;
  }

  function recv() {
    $servers = array($this->getSocketClient());

    $data = "";
    do {
      while (@stream_select($servers, $write = null, $except = null, 5) === false) {
        ;
      }
      $buf = stream_get_contents($this->_socket_client);
      $data .= $buf;
    } while ($buf);

    return $data;
  }

  function send($data) {
    fwrite($this->getSocketClient(), $data, strlen($data));
  }
}
