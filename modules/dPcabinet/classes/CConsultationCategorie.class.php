<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

class CConsultationCategorie extends CMbObject {
  
  // DB Table key
  var $categorie_id = null;

  // DB References
  var $function_id = null;
   
  // DB fields
  var $nom_categorie = null;
  var $nom_icone     = null;
  var $duree         = null;
  var $commentaire   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'consultation_cat';
    $spec->key   = 'categorie_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["function_id"]   = "ref notNull class|CFunctions";
    $specs["nom_categorie"] = "str notNull";
    $specs["nom_icone"]     = "str notNull";
    $specs["duree"]         = "num min|1 max|15 notNull default|1 show|0";
    $specs["commentaire"]   = "text helped seekable";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["consultations"] = "CConsultation categorie_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom_categorie;
  }  
}

?>