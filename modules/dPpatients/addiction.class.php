<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

class CAddiction extends CMbMetaObject {
  // DB Table key
  var $addiction_id = null;

  // DB fields
  var $type      = null;
  var $addiction = null;
  
  function CAddiction() {
    $this->CMbObject("addiction", "addiction_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["object_id"   ] = "notNull ref class|CDossierMedical meta|object_class";
    $specs["object_class"] = "notNull enum list|CPatient|CConsultAnesth";
    $specs["type"        ] = "notNull enum list|tabac|oenolisme|cannabis";
    $specs["addiction"   ] = "text";
    return $specs;
  }

  function getHelpedFields(){
    return array(
      "addiction" => "type"
    );
  }
}
?>