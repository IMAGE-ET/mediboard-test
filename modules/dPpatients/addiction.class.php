<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

class CAddiction extends CMbObject {
  // DB Table key
  var $addiction_id = null;

  // DB fields
  var $type      = null;
  var $addiction = null;
  var $dossier_medical_id = null;
  
  function CAddiction() {
    $this->CMbObject("addiction", "addiction_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["type"        ] = "notNull enum list|tabac|oenolisme|cannabis";
    $specs["addiction"   ] = "text";
    $specs["dossier_medical_id"] = "ref class|CDossierMedical";
    return $specs;
  }

  function getHelpedFields(){
    return array(
      "addiction" => "type"
    );
  }
}
?>