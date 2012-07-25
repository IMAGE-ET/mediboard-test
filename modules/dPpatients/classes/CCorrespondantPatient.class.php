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
  var $nom_jeune_fille = null;
  var $prenom     = null;
  var $naissance  = null;
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
    $specs["patient_id"] = "ref notNull class|CPatient";
    $specs["relation"]   = "enum list|assurance|autre|confiance|employeur|inconnu|prevenir";
    $specs["relation_autre"] = "str";
    $specs["nom"]        = "str confidential";
    $specs["nom_jeune_fille"] = "str";
    $specs["prenom"]     = "str";
    $specs["naissance"]  = "date";
    $specs["adresse"]    = "text";
    $specs["cp"]         = "numchar minLength|4 maxLength|5";
    $specs["ville"]      = "str confidential";
    $specs["tel"]        = "phone confidential";
    $specs["mob"]        = "phone confidential";
    $specs["fax"]        = "phone confidential";
    $specs["urssaf"]     = "numchar length|11 confidential";
    $specs["parente"]    = "enum list|ami|ascendant|autre|beau_fils|colateral|collegue|compagnon|conjoint|directeur|divers|employeur|employe|enfant|enfant_adoptif|entraineur|epoux|frere|grand_parent|mere|pere|petits_enfants|proche|proprietaire|soeur|tuteur";
    $specs["parente_autre"] = "str";
    $specs["email"]      = "str maxLength|255";
    $specs["remarques"]  = "text";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->relation ?
      CAppUI::tr("CCorrespondantPatient.relation.".$this->relation) :
      $this->relation_autre;
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