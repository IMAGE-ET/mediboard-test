<?php /* $Id$ */

/**
 * 
 * une modification
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CFavoriCCAM Class
 */
class CFavoriCCAM extends CMbObject {
  // DB Table key
	var $favoris_id = null;
  
  // DB References
	var $favoris_user = null;

  // DB fields
  var $favoris_code = null;

	function CFavoriCCAM() {
		$this->CMbObject("ccamfavoris", "favoris_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    
    $this->_props["favoris_user"] = "ref|notNull";
    $this->_props["favoris_code"] = "str|notNull";
    
    $this->_seek["favoris_code"] = "equal";
	}

  function check() {
    $sql = "SELECT *" .
      "\nFROM `ccamfavoris`" .
      "\nWHERE `favoris_code` = '$this->favoris_code'" .
      "\nAND `favoris_user` = '$this->favoris_user'";
    $copies = db_loadList($sql);

    if (count($copies))
      return "le favori existe déjà";
    
     return parent::check();
 }
}
?>