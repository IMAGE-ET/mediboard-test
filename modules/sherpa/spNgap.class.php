<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

CAppUI::requireModuleClass("sherpa", "spObject");

/**
 * Détail CCAM pour Sherpa, correspond à un acte CCAM Mediboard
 */
class CSpNGAP extends CSpObject {  
  // DB Table key
  var $idacte = null;

  // DB Fields : see getProps();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CActeNGAP';
    $spec->table   = 'es_ngap';
    $spec->key     = 'idacte';
    return $spec;
  }
 	
  function makeId() {
    $ds = $this->getCurrentDataSource();
    $query = "SELECT MAX(`{$this->_spec->key}`) FROM {$this->_spec->table}";
    $latestId = $ds->loadResult($query);
    $this->_id = $latestId+1;
  }

  function getProps() {
    $specs = parent::getProps();
    
    $specs["idacte"]   = "num";                  /* Numéro d'acte                */
    $specs["idinterv"] = "num";                  /* Numéro d'intervention        */
    $specs["numdos"]   = "numchar length|6";     /* Numéro de dossier            */
    $specs["malnum"]   = "numchar length|6";     /* Numéro de malade             */
    $specs["pracod"]   = "str length|3";         /* Code du praticien            */
//    $specs["prescr"]   = "str length|7";         /* Code du prescripteur         */
    $specs["datact"]   = "str length|19";        /* Date heure de l'acte         */
    $specs["codact"]   = "str maxLength|3";      /* Code Lettre                  */
    $specs["actqte"]   = "str maxLength|64";     /* Coefficient libellé          */
    $specs["quant" ]   = "float";                /* Quantité réelle              */
    $specs["coeff" ]   = "float";                /* Coefficient                    */
    $specs["depass"]   = "bool";                 /* Dépassement d'honoraire      */
    $specs["valdep"]   = "currency";             /* Valeur du dépassement        */
    $specs["nuit"  ]   = "bool";                 /* Acte de nuit                 */
    $specs["ferie" ]   = "bool";                 /* Acte un jour férié           */
    $specs["gratuit"]  = "bool";                 /* Acte gratuit                 */
    $specs["flag"]     = "str length|1";         /* Flag                         */
    $specs["datmaj"]   = "str length|19";        /* Date de derniere mise a jour */
    
		return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->_id (Malade: $this->malnum, Dossier: $this->numdos)";
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
    $acte = new CActeNGAP();
    
    return $acte;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof  $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $acte = $mbObject;
    
    // Sejour
    $acte->loadRefSejour();
    if ($sejour =& $acte->_ref_sejour) {
      $idSejour = CSpObjectHandler::getId400For($sejour);
      $this->numdos = $idSejour->id400;
    }
    
    // Patient
    $acte->loadRefPatient();
    $patient =& $acte->_ref_patient;
    $idPatient = CSpObjectHandler::getId400For($patient);
    $this->malnum = $idPatient->id400;
    
    // Exécutant
    $acte->loadRefExecutant();
    $executant =& $acte->_ref_executant;
    $idExecutant = CSpObjectHandler::getId400For($executant);
    $this->pracod = $idExecutant->id400;  
    
    // Contenu
    $acte->loadExecution();
    $this->datact = mbDateToLocale($acte->_execution);
    $this->codact = CSpObject::makeString($acte->code);
    $this->quant  = $acte->quantite;
    $this->actqte = $acte->coefficient;
    $this->coeff  = $acte->coefficient;

    if ($acte->demi) {
      $this->actqte .= "/2";
      $this->coeff  /= 2;
    }
    
    $this->depass = $acte->montant_depassement > 0 ? "1": "0";
    $this->valdep = $acte->montant_depassement > 0 ? $acte->montant_depassement : "0";
    $this->gratuit = $acte->_montant_facture == 0.0 ? "1" : "0";
    $this->nuit  = $acte->complement == "N" ? "1" : "0";
    $this->ferie = $acte->complement == "F" ? "1" : "0";
    $this->flag = "1";
    
    // Mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>