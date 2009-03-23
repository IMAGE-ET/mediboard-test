<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2249 $
* @author Romain Ollivier
*/

CAppUI::requireModuleClass("sherpa", "spObject");

/**
 * Détail CCAM pour Sherpa, correspond à un acte CCAM Mediboard
 */
class CSpDetCCAM extends CSpObject {  
  // DB Table key
  var $idacte = null;

  // DB Fields : see getProps();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CActeCCAM';
    $spec->table   = 'es_detccam';
    $spec->key     = 'idacte';
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
    
    $specs["idacte"]   = "num";                /* Numéro d'acte                */
    $specs["idinterv"] = "num";                /* Numéro d'intervention        */
    $specs["numdos"] = "numchar length|6";     /* Numéro de dossier            */
    $specs["malnum"] = "numchar length|6";     /* Numéro de malade             */
    $specs["codpra"] = "str length|3";         /* Code du praticien            */
    $specs["codact"] = "str length|7";         /* Code CCAM                    */
    $specs["activ" ] = "num length|1";         /* Activité                     */
    $specs["phase" ] = "num length|1";         /* Phase                        */
    $specs["modt1" ] = "str length|1";         /* Modificateur 1               */
    $specs["modt2" ] = "str length|1";         /* Modificateur 2               */
    $specs["modt3" ] = "str length|1";         /* Modificateur 3               */
    $specs["modt4" ] = "str length|1";         /* Modificateur 4               */
    $specs["assoc" ] = "num length|1";         /* Code d'association           */
    $specs["dephon"] = "currency";             /* Dépassement honoraire        */
    $specs["datact"] = "str length|19";        /* Date heure de l'acte         */
    $specs["extdoc"] = "enum list|1|2|3|4|5|6";/* Extension doc. anesthésie    */
    $specs["rembex"] = "bool";                 /* Remboursement exceptionnel   */
    $specs["codsig"] = "bool";                 /* Signature de l'acte          */
    
    $specs["datmaj"] = "str length|19"; /* Date de derniere mise a jour */
    
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
    $acte = new CActeCCAM();
    $acte->_adapt_object = true;
    
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
    $this->codpra = $idExecutant->id400;  
    
    // Extension documentataire
    if ($acte->code_activite == "4" && $acte->_ref_object instanceof COperation) {
      $operation =& $acte->_ref_object;
      
      if ($type_anesth = $operation->_ref_type_anesth) {
        $this->extdoc = $type_anesth->ext_doc;
      }
    }
    
    // Contenu
    $this->codact = $acte->code_acte;
    $this->activ  = $acte->code_activite;
    $this->phase  = $acte->code_phase;
    $this->assoc  = $acte->code_association;
    $this->modt1 = @$acte->_modificateurs[0];
    $this->modt2 = @$acte->_modificateurs[1];
    $this->modt3 = @$acte->_modificateurs[2];
    $this->modt4 = @$acte->_modificateurs[3];   
    $this->datact = mbDateToLocale($acte->execution);
    $this->dephon = $acte->montant_depassement;
    $this->codsig = $acte->signe;
    $this->rembex = $acte->_rembex;
    
    // Mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
  
  /**
   * Unserialize acte from token with format
   * CODPRA|CODACT|ACTIV|PHASE|MOD1|MOD2|MOD3|MOD4|ASSOC|DEPHON|DATEACT|EXTDOC|REMBEX|CODSIG
   * @param $token string Token
   * @return CActeCCAM 
   */
  static function mapFromToken(CCodable $codable, $token) {
    $acte = new CActeCCAM();
    $acte->setObject($codable);
    
    list ($CODPRA, $CODACT, $ACTIV, $PHASE, $MOD1, $MOD2, $MOD3, $MOD4, $ASSOC, $DEPHON, $DATEACT, $EXTDOC, $REMBEX, $CODSIG) = 
      explode("|", $token);
      
    $acte->code_acte = $CODACT;
    $acte->code_activite = $ACTIV;
    $acte->code_phase = $PHASE;

    $acte->executant_id = CSpObjectHandler::getMbObjectFor("CMediusers", $CODPRA);
    return $acte;
  }
}

?>