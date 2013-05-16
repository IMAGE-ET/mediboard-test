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
 * Facture lie à un sejour
 */
class CFactureEtablissement extends CFacture {
  
  // DB Table key
  public $facture_id;
  
  // DB Fields
  public $dialyse;
  public $temporaire;
  
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_etablissement';
    $spec->key   = 'facture_id';
    return $spec;
  }
  
  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["reglements_fact_etab"] = "CReglement object_id";
    $backProps["relance_fact_etab"]    = "CRelance object_id";
    $backProps["facture_liaison_etab"] = "CFactureLiaison facture_id";
    return $backProps;
  }
  
  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["dialyse"]     = "bool default|0";
    $props["temporaire"]  = "bool default|0";
    return $props;
  }
  
  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = sprintf("SE%08d", $this->_id);
  }
  
  /**
   * Redefinition du store
   * 
   * @return void
  **/
  function store() {
    $this->loadRefsReglements();
    $this->loadRefsRelances();
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
  }

  /**
   * Redefinition du delete
   * 
   * @return void
  **/
  function delete() {
    $this->loadRefsReglements();
    $this->loadRefsRelances();
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }
  }
  
  /**
   * Chargement des reglements de la facture
   * 
   * @param bool $cache cache
   * 
   * @return CReglement
  **/
  function loadRefsReglements($cache = 1) {
    $this->_ref_reglements = $this->loadBackRefs("reglements_fact_etab", 'date');
    return parent::loadRefsReglements($cache);
  }
  
  /**
   * Fonction permettant de partir d'un numero de reference de retrouver la facture correspondante
   * 
   * @param string $num_reference le numero de reference 
   * 
   * @return CFactureEtablissement
  **/
  function findFacture($num_reference){
    $facture = new CFactureEtablissement();
    $facture->num_reference = $num_reference;
    $facture->loadMatchingObject();
    return $facture;
  }
  
  /**
   * Relances emises pour la facture
   * 
   * @return CRelance
  **/
  function loadRefsRelances(){
    $this->_ref_relances = $this->loadBackRefs("relance_fact_etab", 'date');
    $this->isRelancable();
    return $this->_ref_relances;
  }
}
