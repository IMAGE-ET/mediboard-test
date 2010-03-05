<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPreferences extends CMbObject {
	var $pref_id = null;
	
	var $user_id = null;
	var $key     = null;
	var $value   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "user_preferences";
    $spec->key   = "pref_id";
    $spec->uniques["uniques"] = array("user_id", "key");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"] = "num min|0"; //"ref class|CUser"; // Needed for the default preferences
    $specs["key"]     = "str notNull maxLength|40";
    $specs["value"]   = "str";
    return $specs;
  }
  
  static function get($user_id = 0) {
  	$pref = new self;
  	$pref->user_id = $user_id;
  	$list = $pref->loadMatchingList();
  	
  	$preferences = array();
  	foreach($list as $_pref) {
  		$preferences[$_pref->key] = $_pref->value;
  	}
  	return $preferences;
  } 
  
  function loadRefsFwd(){
  	$this->loadRefUser();
  }
  
  function loadRefUser(){
  	return $this->loadFwdRef("user_id", true);
  }
}
?>