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
 * Entête CCAM pour Sherpa, correspond à une intervention Mediboard
 */
class CSpDetCCAM extends CSpObject {  
  // DB Table key
  var $idacte = null;

  // DB Fields : see getSpecs();
  
	function CSpDetCCAM() {
	  $this->CSpObject("es_detccam", "idacte");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CActeCCAM";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();
    
    $specs["idinterv"] = "numchar length|6"; /* Numéro de dossier            */
    $specs["numdos"] = "numchar length|6"; /* Numéro de dossier            */
    $specs["malnum"] = "numchar length|6"; /* Numéro de malade             */
    $specs["codpra"] = "str length|3";     /* Code du praticien            */
    $specs["codact"] = "str length|7";     /* Code CCAM                    */
    $specs["activ"] = "num length|1";      /* Activité                     */
    $specs["phase"] = "num length|1";      /* Phase                        */
    $specs["modt1"] = "num length|1";      /* Modificateur 1               */
    $specs["modt2"] = "num length|1";      /* Modificateur 2               */
    $specs["modt3"] = "num length|1";      /* Modificateur 3               */
    $specs["modt4"] = "num length|1";      /* Modificateur 4               */
    $specs["assoc"] = "num length|1";      /* Code d'association           */
    
    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */
    
		return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->_id (Malade: $this->malnum, Dossier: $this->numdos)";
  }
  
  function mapTo() {
    $acte = new CActeCCAM();
    $acte->_adapt_object = true;
    
//    mbDump($this->getProps(), $this->_class_name);
    
    // Opération
    $operation = CSpObjectHandler::getMbObjectFor("COperation", $this->idinterv);
    $acte->setObject($operation);
    
    // Execution
    $operation->loadRefPlageOp();
    $acte->execution = $operation->_datetime_reel;

    // Executant
	  $executant = CSpObjectHandler::getMbObjectFor("CMediusers", $this->codpra);        
    $acte->executant_id = $executant->_id;
    
    // Contenu
    $acte->code_acte     = $this->codact;
    $acte->code_activite = $this->activ;
    $acte->code_phase    = $this->phase;
    $acte->modificateurs = trim("$this->modt1$this->modt2$this->modt3$this->modt4");
    
    $acte->code_association = $this->assoc;

//    mbDump($acte->getProps(), $acte->_class_name);
    
    return $acte;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!is_a($mbObject, $mbClass)) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $acte = $mbObject;
    $acte->loadRefsFwd();
    
    // Mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());

    
    mbDump($this->getProps());
  }
}

?>