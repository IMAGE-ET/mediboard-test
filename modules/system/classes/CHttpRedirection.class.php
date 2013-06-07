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
 * The CHttpRedirection Class
 */
class CHttpRedirection extends CMbObject {
  // DB Table key
  public $http_redirection_id;

  // DB Fields
  public $priority;
  public $from;
  public $to;

  // Other Fields
  public $_complete_from;
  public $_complete_to;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'http_redirection';
    $spec->key   = 'http_redirection_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["priority"] = "num notNull default|0";
    $props["from"]     = "str notNull";
    $props["to"]       = "str notNull";
    return $props;
  }

  /**
   * Traduction de l'Url
   *
   * @return void
   */
  function parseUrl() {
    if (!$this->_complete_from) {
      $this->_complete_from = parse_url($this->from);
    }
    if (!$this->_complete_to) {
      $this->_complete_to = parse_url($this->to);
    }
  }

  /**
   * Effectue la redirection d'après la règle
   *
   * @return bool
   */
  function applyRedirection() {
    $scheme = "http".(isset($_SERVER["HTTPS"]) ? "s" : "");
    $host   = $_SERVER["SERVER_NAME"];
    $port   = ($_SERVER["SERVER_PORT"] == 80) ? "" : ":{$_SERVER['SERVER_PORT']}";
    $params = $_SERVER["REQUEST_URI"];
    $this->parseUrl();

    if ($this->_complete_to["scheme"] == $scheme && $this->_complete_to["host"] == $host) {
      return true;
    }

    if ($this->from == "*") {
      header("Location: $this->to$params");
      CApp::rip();
    }

    if ($this->_complete_from["scheme"] == $scheme && $this->_complete_from["host"] == $host) {
      $scheme = $this->_complete_to["scheme"];
      $host   = $this->_complete_to["host"];
      $redirection = $scheme."://".$host;
      $redirection .= $params;
      header("Location: $redirection");
      CApp::rip();
    }

    return false;
  }
}
