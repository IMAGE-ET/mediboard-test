<?php

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CSyslogSource
 */
class CSyslogSource extends CSocketSource {
  /** @var integer Primary key */
  public $syslog_source_id;

  /** @var string Syslog source hostname */
  public $host;

  /** @var integer Syslog source port to use */
  public $port;

  /** @var string Syslog source protocol to use */
  public $protocol;

  /** @var integer Syslog source timeout connection */
  public $timeout;

  public $ssl_certificate;
  public $ssl_passphrase;
  public $iv_passphrase;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = "syslog_source";
    $spec->key   = "syslog_source_id";

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props                    = parent::getProps();
    $props["port"]            = "num default|514 notNull";
    $props["protocol"]        = "enum list|TCP|UDP|TLS default|TCP notNull";
    $props["timeout"]         = "num default|5";
    $props["ssl_certificate"] = "str";
    $props["ssl_passphrase"]  = "password show|0 loggable|0";
    $props["iv_passphrase"]   = "str show|0 loggable|0";

    return $props;
  }

  function updateEncryptedFields() {
    if ($this->ssl_passphrase === "") {
      $this->ssl_passphrase = null;
    }
    else {
      if (!empty($this->ssl_passphrase)) {
        $this->ssl_passphrase = $this->encryptString($this->ssl_passphrase, "iv_passphrase");
      }
    }
  }

  /**
   * @see parent::isSecured()
   */
  function isSecured() {
    return (($this->protocol == 'TLS') && $this->ssl_certificate && is_readable($this->ssl_certificate));
  }

  /**
   * @see parent::getProtocol()
   */
  function getProtocol() {
    return strtolower($this->protocol);
  }

  function connect() {
    if ($this->_socket_client) {
      return $this->_socket_client;
    }

    return $this->getSocketClient();
  }

  function sendMessage($msg) {
    if (!$this->_socket_client) {
      $this->connect();
    }

    try {
      $this->send($msg);
    }
    catch (Exception $e) {
      CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING);
    }
  }

  function sendTestMessage() {
    $msg = '<107>1 ' . CMbDT::format(null, '%Y-%m-%dT%H:%M:%SZ') . ' MEDIBOARD This is a Syslog message sample';
    $this->sendMessage($msg);
  }

  /**
   * @see parent::isReachableSource()
   *
   * @return bool
   */
  function isReachableSource() {
    // UDP
    if ($this->protocol == 'UDP') {
      try {
        $this->testUDPConnection();
        $this->_reachable = 2;
      }
      catch (CMbException $e) {
        $this->_reachable = 0;
        $this->_message   = $e->getMessage();

        return false;
      }
    }
    // TCP, TLS
    else {
      try {
        $this->connect();
        $this->_reachable = 2;
      }
      catch (CMbException $e) {
        $this->_reachable = 0;
        $this->_message   = $e->getMessage();

        return false;
      }
    }

    return true;
  }

  /**
   * Write some data in order to check if UDP port is open (Not really reliable)
   *
   * @return bool
   * @throws CMbException
   */
  function testUDPConnection() {
    $handle = fsockopen("udp://$this->host", $this->port, $errno, $errstr, 2);

    if (!$handle) {
      throw new CMbException("$errno : $errstr");
    }

    socket_set_timeout($handle, $this->timeout);
    $write = fwrite($handle, "x00");

    if (!$write) {
      throw new CMbException("common-error-Unable to write to port.");
    }

    $start_time = time();
    $header     = fread($handle, 1);
    $endTime    = time();
    $time_diff  = $endTime - $start_time;

    fclose($handle);
    if ($time_diff < $this->timeout) {
      throw new CMbException("common-error-Unreachable source", $this->name);
    }
  }
}
