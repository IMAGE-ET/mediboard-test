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
 * Entête CCAM pour Sherpa, correspond à une intervention Mediboard
 */
class CSpEntCCAM extends CSpObject {  
  // DB Table key
  var $idinterv = null;

  // DB Fields : see getProps();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CCodable';
    $spec->table   = 'es_entccam';
    $spec->key     = 'idinterv';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();
    
    $specs["idinterv"] = "num";            /* Numéro d'intervention        */
    $specs["numdos"] = "numchar length|6"; /* Numéro de dossier            */
    $specs["malnum"] = "numchar length|6"; /* Numéro de malade             */
    $specs["debint"] = "str length|19";    /* Début intervention           */
    $specs["finint"] = "str length|19";    /* Fin intervention             */
    $specs["datope"] = "str length|19";    /* Entrée en salle              */
    $specs["finope"] = "str length|19";    /* Sortie de salle              */
    $specs["pracod"] = "str length|3";     /* Code du chirugien            */
    $specs["codane"] = "str length|3";     /* Code de l'anesthésiste       */
    $specs["codsal"] = "str length|2";     /* Code de la salle d'op        */

	  $specs["codpan"]   = "str length|3";   /* Code panseuse                */
    for ($i = 1; $i <= 3; $i++) {
	    $specs["aidop$i"] = "str length|3";  /* Code aide opératoire         */
	    $specs["dhaid$i"] = "str length|19"; /* Début aide opératoire        */
	    $specs["fhaid$i"] = "str length|19"; /* Fin aide opératoire          */
    }

    $specs["valigs"] = "num"             ; /* Score IGS                    */
    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */
    
		return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->_id (Malade: $this->malnum, Dossier: $this->numdos)";
  }
  
  function makeId(CCodable $codable) {
    if ($codable instanceof CSejour) {
      $this->_id = "0";
      return;
    }
      
    $ds = $this->getCurrentDataSource();
    $query = "SELECT MAX(`{$this->_spec->key}`) FROM {$this->_spec->table}";
    $latestId = $ds->loadResult($query);
    $this->_id = $latestId+1;
  }

  /**
   * Supprime entêtes CCAM pour le dossier
   */
  function deleteForDossier($numdos) {
    $ds = $this->getCurrentDataSource();

    $query = "SELECT COUNT(*) FROM {$this->_spec->table} WHERE numdos = '$numdos'";
    $count = $ds->loadResult($query);

    $query = "DELETE FROM {$this->_spec->table} WHERE numdos = '$numdos'";
    $ds->exec($query);

    return $count;
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
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $codable = $mbObject;
    
    // Sejour
    $codable->loadRefSejour();
    $idSejour = CSpObjectHandler::getId400For($codable->_ref_sejour);
    $this->numdos = $idSejour->id400;
    
    // Patient
    $codable->loadRefPatient();
    $idPatient = CSpObjectHandler::getId400For($codable->_ref_patient);
    $this->malnum = $idPatient->id400;
    
    // Dates
    if ($mbObject instanceof COperation) {
      $operation =& $mbObject;
	    $operation->loadRefPlageOp();
	    $date = mbDate($operation->_datetime);
	    
	    // Opération
	    $deb_op = mbGetValue($operation->debut_op, "00:00:00");
	    $fin_op = mbGetValue($operation->fin_op  , "00:00:00");
	    $this->datope = mbDateToLocale("$date $deb_op");
	    $this->finope = mbDateToLocale("$date $fin_op");
	    
	    // Intervention = salle
	    $deb_int = mbGetValue($operation->entree_salle, "00:00:00");
	    $fin_int = mbGetValue($operation->sortie_salle, "00:00:00");
	    $this->debint = mbDateToLocale("$date $deb_int");
	    $this->finint = mbDateToLocale("$date $fin_int");
    }
    
    // Chirurgien
    $mbObject->loadRefPraticien();
    $idPrat = CSpObjectHandler::getId400For($mbObject->_ref_praticien);
    $this->pracod = $idPrat->id400;
	    
    // Anesthésiste 
    if ($mbObject instanceof COperation) {
      $operation =& $mbObject;
			
			 // Déjà chargé par la plage op
			if ($operation->anesth_id) {
	      $idAnesth = CSpObjectHandler::getId400For($operation->_ref_anesth);
			  $this->codane = $idAnesth->id400;
			}
    }
	    
    // Salle de l'intervention
    if ($mbObject instanceof COperation) {
      $operation =& $mbObject;
      if ($operation->_ref_salle->_id) {
		    $idSalle = CSpObjectHandler::getId400For($operation->_ref_salle);
		    $this->codsal = $idSalle->id400;
	    }
    }
    
    // Aides opératoire et panseuse
    $mbObject->loadAffectationsPersonnel();
    $affectations_op       = mbGetValue($mbObject->_ref_affectations_personnel["op"], array());
		$affectations_panseuse = mbGetValue($mbObject->_ref_affectations_personnel["op_panseuse"], array());
		
		if(!$affectations_op){
		  $affectations_op = array();
		}
    if(!$affectations_panseuse){
		  $affectations_panseuse = array();
		}
		
		// Panseuse
    if($affectationPanseuse = reset($affectations_panseuse)) {
      $affectationPanseuse->loadRefPersonnel();
      $personnel = $affectationPanseuse->_ref_personnel;
      $personnel->loadRefUser();
      
      $idPans = CSpObjectHandler::getId400For($personnel->_ref_user);
      $this->codpan = $idPans->id400;
    }
		
		// Aides opératoires
	
    $aidopNumber = 0;
    foreach ($affectations_op as $affectationPersonnel) {
      if (++$aidopNumber <=  3) {
        $affectationPersonnel->loadRefPersonnel();
        $personnel = $affectationPersonnel->_ref_personnel;
        $personnel->loadRefUser();
        
        $idAidop = CSpObjectHandler::getId400For($personnel->_ref_user);
        $aidopField = "aidop$aidopNumber";
        $dhaidField = "dhaid$aidopNumber";
        $fhaidField = "fhaid$aidopNumber";
        $this->$aidopField = $idAidop->id400;
        $this->$dhaidField = mbDateToLocale($affectationPersonnel->debut);
        $this->$fhaidField = mbDateToLocale($affectationPersonnel->fin);
      }
    }
        
    // IGS
    $this->valigs = 0;
    switch ($mbObject->_class_name) {
      case "COperation":
      $operation =& $mbObject;
      $operation->loadRefsConsultAnesth();
      $consult_anesth =& $operation->_ref_consult_anesth;
      $consult_anesth->loadRefConsultation();
      $consult =& $consult_anesth->_ref_consultation;
      $consult->loadRefsExamIgs();
      $examigs =& $consult->_ref_examigs;
      $this->valigs = $examigs->scoreIGS;
      break;
    }
        
    // Mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>