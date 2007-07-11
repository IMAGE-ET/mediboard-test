<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

class CTraitement extends CMbMetaObject {
  // DB Table key
  var $traitement_id = null;

  // DB fields
  var $debut      = null;
  var $fin        = null;
  var $traitement = null;
  
  function CTraitement() {
    $this->CMbObject("traitement", "traitement_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["object_id"   ] = "notNull ref class|CDossierMedical meta|object_class";
    $specs["object_class"] = "notNull enum list|CPatient|CConsultAnesth";
    $specs["debut"       ] = "date";
    $specs["fin"         ] = "date moreEquals|debut";
    $specs["traitement"  ] = "text";
    return $specs;
  }

  function getSeeks() {
    return array (
      "traitement" => "like"
    );
  }

  function getHelpedFields(){
    return array(
      "traitement" => null
    );
  }
  
}

?>