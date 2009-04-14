<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
* Preferences class
*/
class CPreferences {
	var $pref_user = null;
	var $pref_name = null;
	var $pref_value = null;
	var $ds = null ; 
    
	function CPreferences() {
		$this->ds = CSQLDataSource::get("std");
		// empty constructor
	}

	function bind($hash) {
		bindHashToObject(stripslashes_deep($hash), $this);
	}

	function check() {
		// TODO MORE
		return null; // object is ok
	}

	function store() {
		$msg = $this->check();
		if( $msg ) {
			return "CPreference::store-check failed<br />$msg";
		}
		if (($msg = $this->delete())) {
			return "CPreference::store-delete failed<br />$msg";
		}
		if (!($ret = $this->ds->insertObject("user_preferences", $this, "pref_user"))) {
			return "CPreference::store failed <br />" . $this->ds->error();
		} else {
			return null;
		}
	}

	function delete() {
		$sql = "DELETE FROM user_preferences WHERE pref_user = '$this->pref_user' AND pref_name = '$this->pref_name'";
		if ($this->ds->exec( $sql )) {
			return $this->ds->error();
		} else {
			return null;
		}
	}
}
?>