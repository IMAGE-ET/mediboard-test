<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

CAppUI::requireModuleClass("sherpa", "spObject");

/**
 * Classe du malade sherpa
 */
class CSpDossier extends CSpObject {  
  // DB Table key
  var $numdos = null;

  // DB Fields : see getProps();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CSejour';
    $spec->table   = 't_dossier';
    $spec->key     = 'numdos';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();
    $specs["topfac"] = "str length|1"    ; /* Date de derniere mise a jour */
    $specs["numdos"] = "numchar length|6"; /* Numero de dossier            */
    $specs["malnum"] = "numchar length|6"; /* Numero de malade             */
    $specs["anndos"] = "str maxLength|2" ; /* 'SH' si séjour annule        */
    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */
    
    return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->numdos ($this->malnum)";
  }
  
  function mapTo() {
    // Load patient
    $malade = new CSpMalade();
    $malade->load($this->malnum); 
    if (!$patient = $malade->loadMbObject()) {
      throw new Exception("Malade '$this->malnum' is not linked to a Mb Patient");
    }
    
    $sejour = new CSejour();
    $sejour->patient_id = $patient->_id;
    $sejour->annule = $this->anndos == 'SH' ? "1" : "0";
    return $sejour;
  }
  
  function isConcernedBy(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
      return false;
    }
    
    return $mbObject->type != "urg" || $mbObject->zt;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
        
    $sejour = $mbObject;
    $sejour->loadRefPatient();
    $idMalade = CSpObjectHandler::getId400For($sejour->_ref_patient);
    
    $this->topfac = "N";
    $this->anndos = "";
    if ($sejour->annule) $this->anndos = "SH";
    if (!$sejour->facturable) $this->anndos = "NF";
      
    $this->malnum = $idMalade->id400;
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>