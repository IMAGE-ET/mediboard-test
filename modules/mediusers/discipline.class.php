<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage mediusers
 *  @version $Revision: $
 *  @author Romain Ollivier
*/

/**
 * The CDiscipline Class
 */
class CDiscipline extends CMbObject {
  // DB Table key
  var $discipline_id = null;

  // DB Fields
  var $text      = null;
  var $categorie = null;

  // Object References
  var $_ref_users = null;
  
  // Form Fields
  var $_compat    = null;
  
  function CDiscipline() {
    parent::__construct();
    
    static $dispo = null;
    if(!$dispo) {
      $dispo = array ();
    }
    $this->_dispo =& $dispo;
    
    static $compat = null;
    if(!$compat) {
      $this->addCompat($compat, "ORT", "ORT", false, false);
  
      $this->addCompat($compat, "ORL", "ORL", false, false);
  
      $this->addCompat($compat, "OPH", "ORT", null, false);
      $this->addCompat($compat, "OPH", "OPH");
  
      $this->addCompat($compat, "DER", "ORT", false, false);
      $this->addCompat($compat, "DER", "OPH", false);
      $this->addCompat($compat, "DER", "DER", true, true);
  
      $this->addCompat($compat, "STO", "DER", null, false);
      $this->addCompat($compat, "STO", "STO");
  
      $this->addCompat($compat, "GAS", "DER", false, false);
      $this->addCompat($compat, "GAS", "GAS");
  
      $this->addCompat($compat, "ARE", "ORT", null, false);
      $this->addCompat($compat, "ARE", "ORL", null, false);
      $this->addCompat($compat, "ARE", "ORT", null, false);
      $this->addCompat($compat, "ARE", "OPH");
      $this->addCompat($compat, "ARE", "DER", null, false);
      $this->addCompat($compat, "ARE", "ARE");
  
      $this->addCompat($compat, "RAD", "ORT", null, false);
      $this->addCompat($compat, "RAD", "ORL", null, false);
      $this->addCompat($compat, "RAD", "ORT", null, false);
      $this->addCompat($compat, "RAD", "OPH");
      $this->addCompat($compat, "RAD", "DER", null, false);
      $this->addCompat($compat, "RAD", "ARE");
      $this->addCompat($compat, "RAD", "RAD");
  
      $this->addCompat($compat, "GYN", "ORT", null, false);
      $this->addCompat($compat, "GYN", "ORL", null, false);
      $this->addCompat($compat, "GYN", "ORT", null, false);
      $this->addCompat($compat, "GYN", "OPH");
      $this->addCompat($compat, "GYN", "DER", null, false);
      $this->addCompat($compat, "GYN", "RAD");
      $this->addCompat($compat, "GYN", "ARE");
      $this->addCompat($compat, "GYN", "GYN");
    }
    $this->_compat =& $compat;
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'discipline';
    $spec->key   = 'discipline_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["users"] = "CMediusers discipline_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["text"]      = "str notNull";
    $specs["categorie"] = "enum list|ORT|ORL|OPH|DER|STO|GAS|ARE|RAD|GYN|EST";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
  }
  
  function loadUsedDisciplines($where = array(), $order = null) {
    $ljoin["users_mediboard"] = "`users_mediboard`.`discipline_id` = `discipline`.`discipline_id`";
    $where["users_mediboard.discipline_id"] = "IS NOT NULL";
    if(!$order) {
      $order = "`discipline`.`text`";
    }
    return $this->loadList($where, $order, null, null, $ljoin);
  }
  
  function updateFormFields () {
    parent::updateFormFields();

    $this->_view = strtolower($this->text);
    if(strlen($this->_view) > 25)
      $this->_shortview = substr($this->_view, 0, 23)."...";
    else
      $this->_shortview = $this->_view;
  }
    
  // Backward references
  function loadRefsBack() {
    $where = array("discipline_id" => "= '$this->discipline_id'");
    $this->_ref_users = new CMediusers;
    $this->_ref_users = $this->_ref_users->loadList($where);
  }
  
  function loadGroupRefsBack() {
    $where = array("discipline_id" => "= '$this->discipline_id'");
    $this->_ref_users = new CMediusers;
    $this->_ref_users = $this->_ref_users->loadGroupList($where);
  }
  
  function addCompat(&$compat, $patho1, $patho2, $septique1 = null, $septique2 = null) {
    assert(in_array($patho1, $this->_enums["categorie"]));
    assert(in_array($patho2, $this->_enums["categorie"]));
    assert($septique1 === null or is_bool($septique1));
    assert($septique2 === null or is_bool($septique2));

    if ($septique1 === null) {
      $this->addCompat($compat, $patho1, $patho2, false, $septique2);
      $this->addCompat($compat, $patho1, $patho2, true , $septique2);
    }

    if ($septique2 === null) {
      $this->addCompat($compat, $patho1, $patho2, $septique1, false);
      $this->addCompat($compat, $patho1, $patho2, $septique1, true );
    }
    
    if ($septique1 === null or $septique2 === null) {
      return;
    }

    @$compat[$patho1][$septique1][$patho2][$septique2] = true;
  }
  
  function isCompat($patho1, $patho2, $septique1, $septique2) {
    
    if(!$patho1 || !$patho2) {
      return true;
    }
    
    assert($septique1 !== null);
    assert($septique2 !== null);

    // bidirectional
    return 
      @$this->_compat[$patho1][$septique1][$patho2][$septique2] or
      @$this->_compat[$patho2][$septique2][$patho1][$septique1];
  }  
}

?>