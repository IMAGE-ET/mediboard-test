<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

CAppUI::requireModuleClass("sherpa", "spObject");

/**
 * Détail CIM pour Sherpa, correspond à une diagnostic Mediboard
 */
class CSpDetCIM extends CSpObject {  
  // DB Table key
  var $iddiag = null;

  // DB Fields : see getProps();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CCodable';
    $spec->table   = 'es_detcim';
    $spec->key     = 'iddiag';
    return $spec;
  }
 	
  function makeId() {
    $ds = $this->getCurrentDataSource();
    $query = "SELECT MAX(`{$this->_spec->key}`) FROM ".$this->_spec->table;
    $latestId = $ds->loadResult($query);
    $this->_id = $latestId+1;
  }

  function getProps() {
    $specs = parent::getProps();
    
    $specs["iddiag"]   = "num";            /* Numéro de diagnostic         */
    $specs["idinterv"] = "num";            /* Numéro d'intervention        */
    $specs["numdos"] = "numchar length|6"; /* Numéro de dossier            */
    $specs["coddia"] = "str maxLength|7";  /* code CIM du diagnostic       */
    $specs["typdia"] = "enum list|P|R|S";  /* type de diagnostic           */
    
    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */
    
		return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->coddia (Interv: $this->idinterv, Dossier: $this->numdos)";
  }
  
  /**
   * Supprimer détails ccam pour le dossier
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
    return null;
  }
  
  /**
   * Mapping pour le seul DP du séjour Mediboard
   */
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $codable = $mbObject;
    
    // Sejour
    $codable->loadRefSejour();
    $sejour =& $codable->_ref_sejour;
    $idSejour = CSpObjectHandler::getId400For($sejour);
    $this->numdos = $idSejour->id400;
        
    // Contenu
    $this->coddia = CSpObject::makeString($sejour->DP);
    $this->typdia = "P";
    
    // Mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>