<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

class CSpActesExporter {
  static $detCCAM = array();
  static $detCIM  = array();
	static $entCCAM = array();
	static $actNGAP = array();
	
	static $delDetCCAM = array();
  static $delDetCIM  = array();
  static $delEntCCAM = array();
  static $delActNGAP = array();
  
  /**
   * Associations entre actes CCAM Mediboard et les détails CCAM Sherpa
   */
	static function exportDetCCAM(CActeCCAM &$acte_ccam, $idinterv) {
	  $spDetCCAM = new CSpDetCCAM();
	  $spDetCCAM->makeId();
	  $spDetCCAM->idinterv = $idinterv;
	  $spDetCCAM->mapFrom($acte_ccam);
	  $spDetCCAM->getCurrentDataSource();
	  self::$detCCAM[$acte_ccam->_id] = $spDetCCAM->store();
	}
  

  /**
   * Associations entre actes CCAM Mediboard et les détails CCAM Sherpa
   */
	static function exportActNGAP(CActeNGAP &$acte_ngap, $idinterv) {
	  $spNGAP = new CSpNGAP();
	  $spNGAP->makeId();
	  $spNGAP->idinterv = $idinterv;
	  $spNGAP->mapFrom($acte_ngap);
	  $spNGAP->getCurrentDataSource();
	  self::$actNGAP[$acte_ngap->_id] = $spNGAP->store();
	}
	
	/**
	 * Associations diagnostics CIM Mediboard et les détails CIM Sherpa
	 */
	static function exportDetsCIM(CCodable &$codable) {
	  $spDetCIM = new CSpDetCIM();
	  $spDetCIM->makeId();
	  $spDetCIM->mapFrom($codable);
	  $spDetCIM->idinterv = $codable->_idinterv;
	  $spDetCIM->getCurrentDataSource();
	
	  self::$detCIM[$codable->_class_name][$codable->_id][] = $spDetCIM->store();
	  
	  $sejour =& $codable->_ref_sejour;
	
	  // Diagnostic relié
	  if ($sejour->DR) {
	    $spDetCIM->makeId();
	    $spDetCIM->typdia = "R";
	    $spDetCIM->coddia = CSpObject::makeString($sejour->DR);
	    $spDetCIM->datmaj = mbDateToLocale(mbDateTime());
	    self::$detCIM[$codable->_class_name][$codable->_id][] = $spDetCIM->store();
	  }
	  
	  // Diagnostics associés
	  $sejour->loadRefDossierMedical();
	  if ($sejour->_ref_dossier_medical->_codes_cim) {
		  foreach ($sejour->_ref_dossier_medical->_codes_cim as $code_cim) {
		    $spDetCIM->makeId();
		    $spDetCIM->typdia = "S";
		    $spDetCIM->coddia = CSpObject::makeString($code_cim);
		    $spDetCIM->datmaj = mbDateToLocale(mbDateTime());
		    self::$detCIM[$codable->_class_name][$codable->_id][] = $spDetCIM->store();
		  }
	  }
	}
	
	/**
	 * Association des info CIM
	 */
	static function exportInfoCIM(COperation &$operation, $key) {
	  if (!$operation->$key) {
	    return;
	  }
	  
	  $spDetCIM = new CSpDetCIM();
	  $spDetCIM->makeId();
	  $spDetCIM->mapFrom($operation);
	  $spDetCIM->idinterv = $operation->_idinterv;
	  $spDetCIM->getCurrentDataSource();
	  $spDetCIM->typdia = "S";
	  $spDetCIM->coddia = CSpObject::makeString($key);
	
	  self::$detCIM[$operation->_class_name][$operation->_id][] = $spDetCIM->store();
	}
	
	/**
	 * Associations entre codable Mediboard et les entêtes CCAM Sherpa
	 */
	static function exportEntCCAM(CCodable &$codable) {
	  $spEntCCAM = new CSpEntCCAM();
	  $spEntCCAM->makeId($codable);
	  $spEntCCAM->mapFrom($codable);
	  $spEntCCAM->getCurrentDataSource();
	  self::$entCCAM[$codable->_class_name][$codable->_id] = $spEntCCAM->store();
	  
	  foreach ($codable->_ref_actes_ccam as &$acte_ccam) {
	    self::exportDetCCAM($acte_ccam, $spEntCCAM->_id);
	  }

	  foreach ($codable->_ref_actes_ngap as &$acte_ngap) {
	    self::exportActNGAP($acte_ngap, $spEntCCAM->_id);
	  }
	  
	  $codable->_idinterv = $spEntCCAM->_id;
	}
	
	/**
	 * Purge des actes et diagnostics pour un dossier donné
	 */
	static function deleteForDossier(CSejour $sejour) {
	  // Suppression des anciens détails CCAM
	  $spDetCCAM = new CSpDetCCAM();
	  self::$delDetCCAM[$sejour->_id] = $spDetCCAM->deleteForDossier($sejour->_num_dossier);
	  
	  // Suppression des anciens actes NGAP
	  $spNGAP = new CSpNGAP();
	  self::$delActNGAP[$sejour->_id] = $spNGAP->deleteForDossier($sejour->_num_dossier);
	  
	  // Suppression des anciens détails CIM
	  $spDetCIM = new CSpDetCIM();
	  self::$delDetCIM[$sejour->_id] = $spDetCIM->deleteForDossier($sejour->_num_dossier);
	  
	  // Suppression des anciens entêtes
	  $spEntCCAM = new CSpEntCCAM();
	  self::$delEntCCAM[$sejour->_id] = $spEntCCAM->deleteForDossier($sejour->_num_dossier);
	}
}

?>