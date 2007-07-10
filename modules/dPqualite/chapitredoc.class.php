<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CChapitreDoc class
 */
class CChapitreDoc extends CMbObject {
  // DB Table key
  var $doc_chapitre_id = null;
    
  // DB Fields
  var $nom  = null;
  var $code = null;

  function CChapitreDoc() {
    $this->CMbObject("doc_chapitres", "doc_chapitre_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["chapitres_ged"] = "CDocGed doc_chapitre_id";
     return $backRefs;
  }
  
  function getSpecs() {
    return array (
      "nom"  => "notNull str maxLength|50",
      "code" => "notNull str maxLength|10"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code ." - " . $this->nom;
    $this->_shortview = $this->code; 
  }
  
}
?>