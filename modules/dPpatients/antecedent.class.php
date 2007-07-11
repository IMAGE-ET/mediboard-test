<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

class CAntecedent extends CMbMetaObject {
  // DB Table key
  var $antecedent_id = null;

  // DB fields
  var $type  = null;
  var $date  = null;
  var $rques = null;
  
  function CAntecedent() {
    $this->CMbObject("antecedent", "antecedent_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["object_id"   ] = "notNull ref class|CDossierMedical meta|object_class";
    $specs["object_class"] = "notNull enum list|CPatient|CConsultAnesth";
    $specs["type"        ] = "notNull enum list|med|alle|trans|obst|chir|fam|anesth|gyn";
    $specs["date"        ] = "date";
    $specs["rques"       ] = "text";
    return $specs;
  }
  
  function getHelpedFields(){
    return array(
      "rques" => "type"
    );
  }
}

?>