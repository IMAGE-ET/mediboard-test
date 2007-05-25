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

  function CModePaiement() {
    $this->CMbObject("mode_paiement", "mode_paiement_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["fiches_compta"] = "CGestionCab mode_paiement_id";
     return $backRefs;
  }
 
  function getSpecs() {
    return array (
      "function_id" => "ref class|CFunctions",
      "nom"         => "notNull str"
    );
  }
  
  function getSeeks() {
    return array (
      "nom" => "like"
    );
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