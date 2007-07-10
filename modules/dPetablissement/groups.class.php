<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPetablissement
 *	@version $Revision$
 *  @author Romain Ollivier
*/

/**
 * The CGroups class
 */
class CGroups extends CMbObject {
  // DB Table key
	var $group_id       = null;	

  // DB Fields
	var $text           = null;
  var $raison_sociale = null;
  var $adresse        = null;
  var $cp             = null;
  var $ville          = null;
  var $tel            = null;
  var $fax            = null;
  var $mail           = null;
  var $web            = null;
  var $directeur      = null;
  var $domiciliation  = null;
  var $siret          = null;
  var $ape            = null;

  // Object References
  var $_ref_functions = null;

  // Form fields
  var $_tel1        = null;
  var $_tel2        = null;
  var $_tel3        = null;
  var $_tel4        = null;
  var $_tel5        = null;
  var $_fax1        = null;
  var $_fax2        = null;
  var $_fax3        = null;
  var $_fax4        = null;
  var $_fax5        = null;
  
  function CGroups() {
    $this->CMbObject("groups_mediboard", "group_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["documents_ged"] = "CDocGed group_id";
      $backRefs["functions"] = "CFunctions group_id";
      $backRefs["menus"] = "CMenu group_id";
      $backRefs["plats"] = "CPlat group_id";
      $backRefs["salles"] = "CSalle group_id";
      $backRefs["sejours"] = "CSejour group_id";
      $backRefs["services"] = "CService group_id";
      $backRefs["stocks"] = "CStock group_id";
      $backRefs["type_repas"] = "CTypeRepas group_id";
     return $backRefs;
  }
  
  function getSpecs() {
    return array (
      "text"           => "notNull str confidential",
      "raison_sociale" => "str maxLength|50",
      "adresse"        => "text confidential",
      "cp"             => "numchar length|5",
      "ville"          => "str maxLength|50 confidential",
      "tel"            => "numchar length|10",
      "directeur"      => "str maxLength|50",
      "domiciliation"  => "str maxLength|9",
      "siret"          => "str length|14",
      "ape"            => "str length|4",
      "mail"           => "email",
      "fax"            => "numchar length|10",
      "web"            => "str"
    );
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
  }
 
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->text;
    if(strlen($this->text) > 25)
      $this->_shortview = substr($this->text, 0, 23)."...";
    else
      $this->_shortview = $this->text;
    
    $this->_tel1 = substr($this->tel, 0, 2);
    $this->_tel2 = substr($this->tel, 2, 2);
    $this->_tel3 = substr($this->tel, 4, 2);
    $this->_tel4 = substr($this->tel, 6, 2);
    $this->_tel5 = substr($this->tel, 8, 2);
    
    $this->_fax1 = substr($this->fax, 0, 2);
    $this->_fax2 = substr($this->fax, 2, 2);
    $this->_fax3 = substr($this->fax, 4, 2);
    $this->_fax4 = substr($this->fax, 6, 2);
    $this->_fax5 = substr($this->fax, 8, 2);
  }
  
  function updateDBFields() {
    if (($this->_tel1 != null) && ($this->_tel2 != null) && ($this->_tel3 != null) && ($this->_tel4 !== null) && ($this->_tel5 !== null)) {
      $this->tel = 
        $this->_tel1 .
        $this->_tel2 .
        $this->_tel3 .
        $this->_tel4 .
        $this->_tel5;
    }
    
    if (($this->_fax1 != null) && ($this->_fax2 != null) && ($this->_fax3 != null) && ($this->_fax4 !== null) && ($this->_fax5 !== null)) {
      $this->fax = 
        $this->_fax1 .
        $this->_fax2 .
        $this->_fax3 .
        $this->_fax4 .
        $this->_fax5;
    }
  }
  

  /**
   * Load functions with given permission
   */
  function loadFunctions($permType = PERM_READ) {
    $this->_ref_functions = CMediusers::loadFonctions($permType, $this->_id);
  }
  
  function loadRefsBack() {
    $this->loadFunctions();
  }

  
  /**
   * Load groups with given permission
   */
  static function loadGroups($permType = PERM_READ) {
    $order = "text";
    $group = new CGroups;
    $groups = $group->loadList(null, $order);

    foreach ($groups as $_id => $group) {
      if (!$group->getPerm($permType)) {
        unset($groups[$_id]);
      }
    }
    
    return $groups;    
  }
}
?>