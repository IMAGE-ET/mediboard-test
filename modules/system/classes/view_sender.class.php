<?php /* $Id: message.class.php 8208 2010-03-04 19:14:03Z lryo $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * View sender class. 
 * @abstract Sends the content of a view on FTP source handling :
 * - FTP source
 * - cron table-like period + offset planning
 * - rotation on destination
 */
class CViewSender extends CMbObject {
  // DB Table key
  var $sender_id = null; 
  
  // DB fields
  var $source_id   = null;
  var $name        = null;
  var $description = null;
  var $params      = null;
  var $period      = null;
  var $offset      = null;
	var $active      = null;
  
  // Form fields
	var $_params = null;
	var $_when   = null;
	var $_active = null;
  var $_url    = null;
  var $_file   = null;
  	
  // Distant properties
	var $_hour_plan = null;
  
  // Object references
  var $_ref_source;
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "view_sender";
    $spec->key   = "sender_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["source_id"  ] = "ref class|CViewSenderSource";
    $props["name"       ] = "str notNull";
    $props["description"] = "text";
    $props["params"     ] = "text notNull";
    $props["period"     ] = "enum list|1|2|3|4|5|6|10|15|20|30";
    $props["offset"     ] = "num min|0 notNull default|0";
    $props["active"     ] = "bool notNull default|0";

    $props["_url"       ] = "str";
    $props["_file"       ] = "str";
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
		$this->_when = "$this->period mn + $this->offset";
  }
	
  function getActive($minute) {
    $period = intval($this->period);
    $offset = intval($this->offset);
    $minute = intval($minute);
  	
    return $this->_active =  $minute % $period == $offset;
  }
  
	function makeHourPlan($minute = null) {
		$period = intval($this->period);
		$offset = intval($this->offset);

		// Hour plan
		foreach (range(0, 59) as $min) {
			$this->_hour_plan[$min] = $min % $period == $offset;
		}

		// Active
    if ($minute !== null) {
      $this->getActive($minute);
    }
    
    return $this->_hour_plan;
	}
	
	function makeUrl($user) {
    $base = CAppUI::conf("base_url");
    $params = array();
    parse_str(strtr($this->params, "\n", "&"), $params);
    $params["login"] = "1";
    $params["username"] = $user->user_username;
    $params["password"] = $user->user_password;
    $params["dialog"] = "1";
    $params["aio"] = "1";
    $query = CMbString::toQuery($params);
    $url = "$base/?$query";
    return $this->_url = $url;  
	}

  function makeFile() {
  	$file = tempnam("", "view");
  	
  	// Store result in $file with wget
  	
  	return $this->_file = $file;
  }
	
}

?>