<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Facture liée à une ou plusieurs consultations
 *
 */
class CFactureCabinet extends CFacture {
  // DB Table key
  public $facture_id;
  
  /**
   * @see parent::getSpec()
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_cabinet';
    $spec->key   = 'facture_id';
    return $spec;
  }
    
  /**
   * @see parent::getBackProps()
  **/
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["reglements"]    = "CReglement object_id";
    $backProps["relance_fact_cabinet"] = "CRelance object_id";
    $backProps["facture_liaison_cab"]  = "CFactureLiaison facture_id";
    $backProps["journal_liaison_cab"]  = "CJournalLiaison object_id";
    $backProps["echeance_cab"]         = "CEcheance object_id";
    $backProps["rejets_cab"]           = "CFactureRejet facture_id";
    return $backProps;
  }
   
  /**
   * @see parent::updateFormFields()
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = sprintf("FA%08d", $this->_id);
    if (CAppUI::conf("ref_pays") == 2) {
      $this->_view .= " /$this->numero";
    }
  }

  /**
   * Chargement des règlements de la facture
   * 
   * @param bool $cache cache
   * 
   * @return CReglement[]
  **/
  function loadRefsReglements($cache = true) {
    $this->_ref_reglements = $this->loadBackRefs("reglements", 'date');
    return parent::loadRefsReglements($cache);
  }
  
  /**
   * @see parent::store()
   */
  function store() {
    $this->loadRefsConsultation();
    // A vérifier pour le == 0 s'il faut faire un traitement
    if ($this->facture !== '0' && $this->_id) {
      foreach ($this->_ref_consults as $_consultation) {
        if ($this->facture == -1 && $_consultation->facture == 1) {
          $_consultation->facture = 0;
          $_consultation->store();
        }
        elseif ($this->facture == 1 && $_consultation->facture == 0) {
          $_consultation->facture = 0;
          $_consultation->store();
        }
      }
    }
  
    $this->loadRefsRelances();
    $this->loadRefsReglements();

    return parent::store();
  }
  
  /**
   * @see parent::delete()
   */
  function delete() {
    $this->_ref_reglements = array();
    $this->_ref_relances = array();
    $this->_count["relance_fact_cabinet"] = 0;
    $this->_count["reglements"] = 0;
    $this->loadRefsReglements();
    $this->loadRefsRelances();

    return parent::delete();
  }
  
  //Ne pas supprimer cette fonction!
  /**
   * loadRefPlageConsult
   * 
   * @return void
  **/
  function loadRefPlageConsult(){
    
  }
  
  /**
   * Fonction permettant à partir d'un numéro de référence de retrouver la facture correspondante
   * 
   * @param string $num_reference le numéro de référence 
   * 
   * @return CFactureCabinet
  **/
  function findFacture($num_reference){
    $facture = new CFactureCabinet();
    $facture->num_reference = $num_reference;
    $facture->loadMatchingObject();
    return $facture;
  }
  
  /**
   * Chargement des relances de la facture
   * 
   * @return CRelance[]
  **/
  function loadRefsRelances(){
    $this->_ref_relances = $this->loadBackRefs("relance_fact_cabinet", 'date');
    $this->isRelancable();
    return $this->_ref_relances;
  }

  /**
   * Chargement des échéances de la facture
   *
   * @return CEcheance[]
   **/
  function loadRefsEcheances() {
    return $this->_ref_echeances = $this->loadBackRefs("echeance_cab", "date");
  }

  /**
   * @see parent::fillTemplate()
   */
  function fillTemplate(&$template) {
    parent::fillLimitedTemplate($template);
  }

}
