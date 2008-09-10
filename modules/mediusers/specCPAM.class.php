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
class CSpecCPAM extends CMbObject {
  // DB Table key
  var $spec_cpam_id = null;

  // DB Fields
  var $text  = null;
  var $actes = null;

  // Object References
  var $_ref_users = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'spec_cpam';
    $spec->key   = 'spec_cpam_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["users"] = "CMediusers spec_cpam_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["text"]  = "notNull str";
    $specs["actes"] = "notNull str";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
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
    $where = array(
      "spec_cpam_id" => "= '$this->spec_cpam_id'");
    $this->_ref_users = new CMediusers;
    $this->_ref_users = $this->_ref_users->loadList($where);
  }
}

?>