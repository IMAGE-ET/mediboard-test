<?php

/**
 * dPpatients
 *  
 * @category dPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CCorrespondantPatient extends CMbObject {
  
  // DB Table key
  var $correspondant_patient_id = null;
  
  // DB Fields
  var $patient_id = null;
  var $relation   = null;
  var $relation_autre = null;
  var $nom        = null;
  var $prenom     = null;
  var $adresse    = null;
  var $cp         = null;
  var $ville      = null;
  var $tel        = null;
  var $mob        = null;
  var $fax        = null;
  var $urssaf     = null;
  var $parente    = null;
  var $parente_autre = null;
  var $email      = null;
  var $remarques  = null;
  
  var $_eai_initiateur_group_id = null;
  
  // Form fields
  var $_ref_patient = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "correspondant_patient";
    $spec->key   = "correspondant_patient_id";
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $phone_number_format = str_replace(' ', 'S', CAppUI::conf("system phone_number_format"));
    
    $phone_number_mask = "";
    if ($phone_number_format != "") {
      $phone_number_mask = " mask|$phone_number_format";
    }
    
    $specs["patient_id"] = "ref notNull class|CPatient";
    $specs["relation"]   = "enum list|assurance|autre|confiance|employeur|inconnu|prevenir";
    $specs["relation_autre"] = "str";
    $specs["nom"]        = "str confidential";
    $specs["prenom"]     = "str";
    $specs["adresse"]    = "text";
    $specs["cp"]         = "numchar minLength|4 maxLength|5";
    $specs["ville"]      = "str confidential";
    $specs["tel"]        = "str confidential pattern|\d+ minLength|10$phone_number_mask";
    $specs["mob"]        = "str confidential pattern|\d+ minLength|10$phone_number_mask";
    $specs["fax"]        = "str confidential pattern|\d+ minLength|10$phone_number_mask";
    $specs["urssaf"]     = "numchar length|11 confidential";
    $specs["parente"]    = "enum list|ami|ascendant|autre|beau_fils|colateral|collegue|compagnon|conjoint|directeur|divers|employeur|employe|enfant|enfant_adoptif|entraineur|epoux|frere|grand_parent|mere|pere|petits_enfants|proche|proprietaire|soeur|tuteur";
    $specs["parente_autre"] = "str";
    $specs["email"]      = "str maxLength|255";
    $specs["remarques"]  = "text";
    
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["correspondants_courrier"] = "CCorrespondantCourrier object_id";
    return $backProps;
  }  
  
  function loadRefPatient() {
    return $this->_ref_patient = $this->loadFwdRef("patient_id");
  }
}