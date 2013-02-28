<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Facture lié à un séjour
 *
 */
class CFactureEtablissement extends CFacture {
  
  // DB Table key
  public $facture_id;
  
  // DB Fields
  public $dialyse;
  public $temporaire;
      
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_etablissement';
    $spec->key   = 'facture_id';
    return $spec;
  }
    
  /**
   * getBackProps
   * 
   * @return $backProps
  **/
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["reglements_fact_etab"]    = "CReglement object_id";
    return $backProps;
  }
   
  /**
   * getProps
   * 
   * @return $props
  **/
  function getProps() {
    $props = parent::getProps();
    $props["dialyse"]     = "bool default|0";
    $props["temporaire"]  = "bool default|0";
    return $props;
  }
     
  /**
   * updateFormFields
   * 
   * @return void
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = sprintf("SE%08d", $this->_id);
  }

  /**
   * loadRefsFwd
   * 
   * @return void
  **/
  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->loadRefSejour();
  } 
  
  /**
   * Redéfinition du store
   * 
   * @return void
  **/
  function store() {
    //Si on cloture la facture création des lignes de la facture
    if ($this->cloture && $this->fieldModified("cloture") && !$this->completeField("cloture")) {
      $this->creationLignesFacture();
    }
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
  }
  
  /**
   * Redéfinition du delete
   * 
   * @return void
  **/
  function delete() {
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }
  }
  
  /**
   * Chargement des différents séjours liées à la facture
   * 
   * @param bool $cache cache
   * 
   * @return void
  **/
  function loadRefSejour($cache = 1) {
    parent::loadRefsObjects();
    return $this->_ref_sejours;
  }
  
  /**
   * loadRefs
   * 
   * @return void
  **/
  function loadRefs(){
    $this->loadRefCoeffFacture();
    $this->loadRefsFwd();
    $this->loadRefsBack();
    $this->loadNumerosBVR();
  }

  /**
   * Chargement des règlements de la facture
   * 
   * @param bool $cache cache
   * 
   * @return $this->_ref_reglements
  **/
  function loadRefsReglements($cache = 1) {
    $this->_ref_reglements = $this->loadBackRefs("reglements_fact_etab", 'date');
        
    return parent::loadRefsReglements($cache);
  }
  
  /**
   * Fonction permettant à partir d'un numéro de référence de retrouver la facture correspondante
   * 
   * @param string $num_reference le numéro de référence 
   * 
   * @return $facture
  **/
  function findFacture($num_reference){
    $facture = new CFactureEtablissement();
    $facture->num_reference = $num_reference;
    $facture->loadMatchingObject();
    return $facture;
  }
}
