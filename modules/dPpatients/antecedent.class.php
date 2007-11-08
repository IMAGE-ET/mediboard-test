<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

class CAntecedent extends CMbObject {
  // DB Table key
  var $antecedent_id = null;

  // DB fields
  var $type               = null;
  var $date               = null;
  var $rques              = null;
  var $dossier_medical_id = null;
  
  function CAntecedent() {
    $this->CMbObject("antecedent", "antecedent_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["type"        ] = "notNull enum list|med|alle|trans|obst|chir|fam|anesth|gyn";
    $specs["date"        ] = "date";
    $specs["rques"       ] = "text";
    $specs["dossier_medical_id"] = "ref class|CDossierMedical";
    return $specs;
  }
  
  function getHelpedFields(){
    return array(
      "rques" => "type"
    );
  }
}

?>