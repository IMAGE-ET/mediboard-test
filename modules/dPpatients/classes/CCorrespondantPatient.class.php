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
  var $nom        = null;
  var $prenom     = null;
  var $adresse    = null;
  var $cp         = null;
  var $ville      = null;
  var $tel        = null;
  var $urssaf     = null;
  var $parente    = null;
  var $email      = null;
  var $remarques  = null;
  
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
    
    $specs["patient_id"] = "ref notNull class|CPatient";
    $specs["relation"]   = "enum list|confiance|prevenir|employeur";
    $specs["nom"]        = "str confidential";
    $specs["prenom"]     = "str";
    $specs["adresse"]    = "text";
    $specs["cp"]         = "numchar minLength|4 maxLength|5";
    $specs["ville"]      = "str confidential";
    $specs["tel"]        = "numchar confidential length|10 mask|$phone_number_format";
    $specs["urssaf"]     = "numchar length|11 confidential";
    $specs["parente"]    = "enum list|conjoint|enfant|ascendant|colateral|divers";
    $specs["email"]      = "str maxLength|255";
    $specs["remarques"]  = "text";
    
    return $specs;
  }
}