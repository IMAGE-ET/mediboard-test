<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CRubrique Class
 */
class CRubrique extends CMbObject {
  // DB Table key
  var $rubrique_id = null;

  // DB Fields
  var $function_id = null;
  var $nom = null;

  // Object References
  var $_ref_function = null;

  function CRubrique() {
    $this->CMbObject("rubrique_gestioncab", "rubrique_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["fiches_compta"] = "CGestionCab rubrique_id";
     return $backRefs;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "function_id" => "ref class|CFunctions",
      "nom"         => "notNull str"
    );
    return array_merge($specsParent, $specs);
  }
  
  function getSeeks() {
    return array (
      "nom" => "like"
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Rubrique '".$this->nom."'";
  }

  // Forward references
  function loadRefsFwd() {
    // fonction (cabinet)
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_function) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_function->getPerm($permType));
  }
}

?>