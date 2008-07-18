<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CAdministration class
 */
class CAdministration extends CMbMetaObject {
  // DB Field
  var $administration_id = null;
  var $administrateur_id = null;  // Utilisateur effectuant l'administration
  var $dateTime          = null;  // Heure de l'administration
  var $quantite          = null;  // Info sur la prise
  var $unite_prise       = null;  // Info sur la prise
  var $commentaire       = null;  // Commentaire sur l'administration
  var $prise_id          = null;
  
  // Form field
  var $_heure = null;
  
  // Object references
  var $_ref_administrateur = null;
  
 
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'administration';
    $spec->key   = 'administration_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "object_id"            => "notNull ref class|CMbObject meta|object_class",
      "object_class"         => "notNull enum list|CPrescriptionLineMedicament|CPrescriptionLineElement",
      "administrateur_id"    => "notNull ref class|CMediusers",
      "prise_id"             => "ref class|CPrisePosologie",
      "quantite"             => "float",
      "unite_prise"          => "text",
      "dateTime"             => "dateTime",
      "commentaire"          => "text"
    );
    return array_merge($specsParent, $specs);
  }

  function updateFormFields(){
  	parent::updateFormFields();
 
  	$this->_heure = substr(mbTime($this->dateTime), 0, 2);
  	if(mbTime($this->dateTime) == "23:59:00"){
  		$this->_heure = "24";
  	}
  }
  
  function loadRefsFwd(){
  	parent::loadRefsFwd();
  	$this->loadRefAdministrateur();
  }
  
  function loadRefAdministrateur(){
  	$this->_ref_administrateur = new CMediusers();
  	$this->_ref_administrateur->load($this->administrateur_id);
  }
}

?>