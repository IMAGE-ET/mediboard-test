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
class CSpEntCCAM extends CSpObject {  
  // DB Table key
  var $idinterv = null;

  // DB Fields : see getSpecs();
  
	function CSpEntCCAM() {
	  $this->CSpObject("es_entccam", "idinterv");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "COperation";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();
    
    $specs["numdos"] = "numchar length|6"; /* Numéro de dossier            */
    $specs["malnum"] = "numchar length|6"; /* Numéro de malade             */
    $specs["debint"] = "str length|19";    /* Début intervention           */
    $specs["finint"] = "str length|19";    /* Fin intervention             */
//    $specs["datope"] = "str length|19";    /* Début intervention            */
//    $specs["finope"] = "str length|19";    /* Fin intervention              */
    $specs["pracod"] = "str length|3";     /* Code du chirugien            */
    $specs["codane"] = "str length|3";     /* Code de l'anesthésiste       */
    $specs["codsal"] = "str length|2";     /* Code de la salle d'op        */

//    $specs["aidop1"] text(3) default ' ',
//    $specs["dhaid1"] date default ' ',
//    $specs["fhaid1"] date default ' ',

//    $specs["aidop2"] text(3) default ' ',
//    $specs["dhaid2"] date default ' ',
//    $specs["fhaid2"] date default ' ',

//    $specs["aidop3"] text(3) default ' ',
//    $specs["dhaid3"] date default ' ',
//    $specs["fhaid3"] date default ' ',

//    $specs["codpan"] text(3) default ' ',
//    $specs["valigs"] integer default 0,
//    $specs["flag"] smallint default 0,
    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */
    
		return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->_id (Malade: $this->malnum, Dossier: $this->numdos)";
  }
  
  function mapTo() {
    $operation = new COperation();
    $operation->cote = "inconnu";
    
    // Sejour
    $sejour = CSpObjectHandler::getMbObjectFor("CSejour", $this->numdos);
    $operation->sejour_id = $sejour->_id;
    
    // Chirurgien
    $operation->chir_id = $sejour->praticien_id;
    if ($this->pracod) {
	    $chirurgien = CSpObjectHandler::getMbObjectFor("CMediusers", $this->pracod);        
	    $operation->chir_id = $chirurgien->_id;
    }
    
    // Anesthésiste
    $anesthesiste = CSpObjectHandler::getMbObjectFor("CMediusers", $this->codane);        
    $operation->anesth_id = $anesthesiste->_id;
    
    // Horodatage
    $deb = mbDateFromLocale($this->debint);
    $fin = mbDateFromLocale($this->finint);
    
    $operation->date = mbDate($deb);
    $operation->debut_op = mbTime($deb);
    $operation->fin_op   = mbTime($fin);
    $operation->temp_operation = mbTimeRelative($operation->debut_op, $operation->fin_op);
    
    return $operation;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!is_a($mbObject, $mbClass)) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $operation = $mbObject;
    
    // Sejour
    $operation->loadRefSejour();
    $sejour =& $operation->_ref_sejour;
    $idSejour = CSpObjectHandler::getId400For($sejour);
    $this->numdos = $idSejour->id400;
    
    // Patient
    $sejour->loadRefPatient();
    $idPatient = CSpObjectHandler::getId400For($sejour->_ref_patient);
    $this->malnum = $idPatient->id400;
    
    // Dates
    $operation->loadRefPlageOp();
    $date = mbDate($operation->_datetime);
    $deb_op = mbGetValue($operation->debut_op, "00:00:00");
    $fin_op = mbGetValue($operation->fin_op  , "00:00:00");
    $this->debint = mbDateToLocale("$date $deb_op");
    $this->finint = mbDateToLocale("$date $fin_op");
    
    // Chirurgien
    $operation->loadRefChir();
    $idChir = CSpObjectHandler::getId400For($operation->_ref_chir);
    $this->pracod = $idChir->id400;
    
    // Anesthésiste 
    // Déjà chargé par la plage op
    if ($operation->anesth_id) {
	    $idAnesth = CSpObjectHandler::getId400For($operation->_ref_anesth);
	    $this->codane = $idAnesth->id400;
    }
    
    // Salle de l'intervention
    if ($operation->_ref_salle->_id) {
	    $idSalle = CSpObjectHandler::getId400For($operation->_ref_salle);
	    $this->pracod = $idChir->id400;
	    $this->codsal = $idSalle->id400;
    }
    
    // Mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>