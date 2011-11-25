<?php

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author
 */

class CCorrespondantCourrier extends CMbObject {
  // DB Table key
  var $correspondant_courrier_id = null;

  // DB References
  var $compte_rendu_id = null;
  
  // DB Fields
  var $nom      = null;
  var $adresse  = null;
  var $cp_ville = null;
  var $email    = null;
  var $active   = null;
  var $tag      = null;
  var $object_class = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'correspondant_courrier';
    $spec->key   = 'correspondant_courrier_id';
    
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["compte_rendu_id"] = "ref class|CCompteRendu notNull cascade";
    $specs["nom"]      = "str";
    $specs["adresse"]  = "text";
    $specs["cp_ville"] = "str";
    $specs["email"]    = "str";
    $specs["active"]   = "bool default|0";
    $specs["tag"]      = "str";
    $specs["object_class"] = "str";
    
    return $specs;
  }
}

?>