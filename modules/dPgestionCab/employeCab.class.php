<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

/**
 * The CGestionCab Class
 */
class CEmployeCab extends CMbObject {
  // DB Table key
  var $employecab_id = null;
  
  // DB References
  var $function_id = null;

  // DB Fields
  var $nom      = null;
  var $prenom   = null;
  var $function = null;
  var $adresse  = null;
  var $cp       = null;
  var $ville    = null;

  // Object References
  var $_ref_function = null;

  function CEmployeCab() {
    $this->CMbObject("employecab", "employecab_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "function_id" => "notNull ref class|CFilesCategory",
      "nom"         => "notNull str",
      "prenom"      => "notNull str",
      "function"    => "notNull str",
      "adresse"     => "str",
      "ville"       => "str",
      "cp"          => "numchar length|5 confidential"
    );
  }
  
  function getSeeks() {
    return array (
      "nom"    => "likeBegin",
      "prenom" => "likeBegin"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->nom $this->prenom";
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