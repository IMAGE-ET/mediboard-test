<?php /* SYSKEYS $Id$ */

##
## CSysKey Class
##

class CSysKey extends CMbObject {
	var $syskey_id    = null;
	var $syskey_name  = null;
	var $syskey_label = null;
	var $syskey_type  = null;
	var $syskey_sep1  = null;
	var $syskey_sep2  = null;

	function CSysKey($name = null, $label = null, $type = "0", $sep1 = "\n", $sep2 = "|") {
		$this->CMbObject("syskeys", "syskey_id");
		$this->syskey_name  = $name;
		$this->syskey_label = $label;
		$this->syskey_type  = $type;
		$this->syskey_sep1  = $sep1;
		$this->syskey_sep2  = $sep2;
	}
}

##
## CSysVal Class
##

class CSysVal extends CMbObject {
	var $sysval_id     = null;
	var $sysval_key_id = null;
	var $sysval_title  = null;
	var $sysval_value  = null;

	function CSysVal( $key = null, $title = null, $value = null ) {
		$this->CMbObject("sysvals", "sysval_id");
		$this->sysval_key_id = $key;
		$this->sysval_title  = $title;
		$this->sysval_value  = $value;
	}
}

?>