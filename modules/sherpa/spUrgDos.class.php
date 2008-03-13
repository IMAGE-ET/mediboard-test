<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2249 $
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("sherpa", "spObject"));

/**
 * Classe du malade sherpa
 */
class CSpDossier extends CSpObject {  
  // DB Table key
  var $numdos = null;

  // DB Fields : see getSpecs();
  
	function CSpDossier() {
	  $this->CSpObject("t_urgdos", "numdos");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CSejour";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["numdos"] = "numchar length|6"; /* Numero de dossier            */
    $specs["malnum"] = "numchar length|6"; /* Numero de malade             */
    $specs["anndos"] = "str maxLength|2" ; /* 'SH' si séjour annule        */
    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */

//    $specs["topfac"] = "str length|1"    ; /* Date de derniere mise a jour */
    
// item    integer4 not null with default, 	/* Dernier no item  facture utilise  */
// datfac  vchar(10) not null with default,	/* Derniere date de facture normale  */
// datfar  vchar(10) not null with default,	/* Derniere date de facture rappel   */
// datavo  vchar(10) not null with default,	/* Derniere date de facture avoir    */
// tofsok  float8 not null with default,   	/* Pas UTILISE                       */

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
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
        
    $sejour = $mbObject;
    $sejour->loadRefPatient();

    // Malade
    $idMalade = CSpObjectHandler::getId400For($sejour->_ref_patient);
    $this->malnum = $idMalade->id400;
    
    // Annulation
    $this->anndos = "";
    if ($sejour->annule) $this->anndos = "SH";
    if (!$sejour->facturable) $this->anndos = "NF";
      
    // Date de mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>