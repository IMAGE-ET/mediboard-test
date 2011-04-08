<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CHttpRedirection Class
 */
class CHttpRedirection extends CMbObject {
  // DB Table key
  var $http_redirection_id = null;
  
  // DB Fields
  var $priority = null;
  var $from     = null;
  var $to       = null;
  
  // Other Fields
  var $_complete_from = null;
  var $_complete_to   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'http_redirection';
    $spec->key   = 'http_redirection_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["priority"] = "num notNull default|0";
    $specs["from"]     = "str notNull";
    $specs["to"]       = "str notNull";
    return $specs;
  }
  
  function parseUrl() {
    if(!$this->_complete_from) {
      $this->_complete_from = parse_url($this->from);
    }
    if(!$this->_complete_to) {
      $this->_complete_to = parse_url($this->to);
    }
  }
  
  function applyRedirection() {
    $scheme = "http".(isset($_SERVER["HTTPS"]) ? "s" : "");
    $host   = $_SERVER["SERVER_NAME"];
    $port   = ($_SERVER["SERVER_PORT"] == 80) ? "" : ":{$_SERVER['SERVER_PORT']}";
    $params = $_SERVER["REQUEST_URI"];
    $this->parseUrl();
    if($this->_complete_to["scheme"] == $scheme && $this->_complete_to["host"] == $host) {
      return true;
    }
    if($this->from == "*") {
      header("Location: $this->to$params");
      return true;
    }
    if($this->_complete_from["scheme"] == $scheme && $this->_complete_from["host"] == $host) {
      $scheme = $this->_complete_to["scheme"];
      $host   = $this->_complete_to["host"];
      $redirection = $scheme."://".$host.$params;
      header("Location: $redirection");
      return true;
    }
    return false;
  }
}

?>