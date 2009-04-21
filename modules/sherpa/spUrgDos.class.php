<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("sherpa", "spObject");

/**
 * Classe du dossier Sherpa UPATOU
 */
class CSpUrgDos extends CSpObject {  
  // DB Table key
  var $numdos = null;

  // DB Fields : see getProps();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CSejour';
    $spec->table   = 't_urgdos';
    $spec->key     = 'numdos';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();
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
  
  function isConcernedBy(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
      return false;
    }
    
    return $mbObject->type == "urg" && !$mbObject->zt;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
        
    $sejour = $mbObject;
    $sejour->_ref_patient = null;
    $sejour->loadRefPatient();
    
    // Malade
    $idMalade = CSpObjectHandler::getId400For($sejour->_ref_patient);
    $this->malnum = $idMalade->id400;
    
    // Annulation
    $this->anndos = "";
    if ($sejour->annule) {
      $this->anndos = "SH";
      $sejour->loadRefRPU();
      if ($sejour->_ref_rpu->mutation_sejour_id) {
        $this->anndos = "HO"; 
      }
    }
    
    // Facturable
    if (!$sejour->facturable) {
      $this->anndos = "NF";
    }
    
    // Date de mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>