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
      $backRefs["0"] = "CDocGed doc_chapitre_id";
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
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "msg-CDocGed-canDelete", 
      "name"      => "doc_ged", 
      "idfield"   => "doc_ged_id", 
      "joinfield" => "doc_chapitre_id"
    );
    
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
}
?>