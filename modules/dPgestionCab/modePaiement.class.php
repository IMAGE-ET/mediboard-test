<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CModePaiement Class
 */
class CModePaiement extends CMbObject {
  // DB Table key
  var $mode_paiement_id = null;

  // DB Fields
  var $function_id = null;
  var $nom         = null;

  // Object References
  var $_ref_function = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'mode_paiement';
    $spec->key   = 'mode_paiement_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["fiches_compta"] = "CGestionCab mode_paiement_id";
    return $backProps;
  }
 
  function getProps() {
  	$specs = parent::getProps();
    $specs["function_id"] = "ref class|CFunctions";
    $specs["nom"]         = "str notNull";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "nom" => "like"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Mode de paiement '".$this->nom."'";
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