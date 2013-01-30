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
  var $facture_id = null;
  
  // DB Fields
  var $dialyse          = null;
      
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
    $props["dialyse"]   = "bool default|0";
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
   * Fonction de création des lignes(items) de la facture lorsqu'elle est cloturée
   * 
   * @return void
  **/
  function creationLignesFacture(){
    parent::loadRefsSejour();
    $this->loadRefCoeffFacture();
    foreach ($this->_ref_sejours as $sejour) {
      foreach ($sejour->_ref_operations as $op) {
        foreach ($op->_ref_actes_tarmed as $acte) {
          $this->creationLigneTarmed($acte, $op->date);
        }
        foreach ($op->_ref_actes_caisse as $acte) {
          $this->creationLigneCaisse($acte, $op->date);
        }
        foreach ($op->_ref_actes_ccam as $acte_ccam) {
          $this->creationLigneCCAM($acte_ccam, $op->date);
        }
        foreach ($op->_ref_actes_ngap as $acte_ngap) {
          $this->creationLigneNGAP($acte_ngap, $op->date);
        }
      }
      foreach ($sejour->_ref_actes_tarmed as $acte) {
        $this->creationLigneTarmed($acte, $sejour->entreee_prevue);
      }
      foreach ($sejour->_ref_actes_caisse as $acte) {
        $this->creationLigneCaisse($acte, $sejour->entreee_prevue);
      }
      foreach ($sejour->_ref_actes_ccam as $acte_ccam) {
        $this->creationLigneCCAM($acte, $sejour->entreee_prevue);
      }
      foreach ($sejour->_ref_actes_ngap as $acte_ngap) {
        $this->creationLigneNGAP($acte, $sejour->entreee_prevue);
      }
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
    parent::loadRefsSejour();

    // Eclatement des factures
    $this->_nb_factures = 1;
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ) {
      $this->eclatementTarmed();
    }
    $this->updateMontants();
            
    return $this->_ref_sejours;
  }

  /**
   * Mise à jour des montant secteur 1, 2 et totaux, utilisés pour la compta
   * 
   * @return void
  **/
  function updateMontants() {
    parent::updateMontants();
    $this->du_patient = 0;
    if (CModule::getActive("tarmed") &&CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ) {
      foreach ($this->_ref_sejours as $sejour) {
        foreach ($sejour->_ref_operations as $op) {
          foreach ($op->_ref_actes_tarmed as $acte) {
            $this->_montant_secteur1 += $acte->montant_base;
            $this->du_patient += $acte->montant_base;
            $this->_montant_secteur2 += $acte->montant_depassement;
            $this->_montant_total    += ($acte->montant_base + $acte->montant_depassement);
          }
          foreach ($op->_ref_actes_caisse as $acte) {
            $this->_montant_secteur1 += $acte->montant_base;
            $this->du_patient += $acte->montant_base;
            $this->_montant_secteur2 += $acte->montant_depassement;
            $this->_montant_total    += ($acte->montant_base + $acte->montant_depassement);
          }
        }
      }
    }
  }

  /**
   * Eclatement des montants de la facture utilisé uniquement en Suisse 
   * 
   * @return void
  **/
  function eclatementTarmed() {
    parent::eclatementTarmed();
    $this->_montant_factures   = array();
    $this->_montant_factures[] = $this->du_patient + $this->du_tiers - $this->remise;
    $this->loadNumerosBVR();
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
  
  /**
   * Chargement des différents numéros de BVR de la facture 
   * 
   * @return void
  **/
  function loadNumerosBVR(){
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") && !count($this->_montant_factures_caisse)) {
      $this->_total_tarmed = 0;
      $this->_total_caisse = 0;
      $this->_autre_tarmed = 0;
      
      foreach ($this->_ref_sejours as $sejour) {
        foreach ($sejour->_ref_actes_tarmed as $acte_tarmed) {
          $this->_total_tarmed += $acte_tarmed->montant_base + $acte_tarmed->montant_depassement;
        }
        foreach ($sejour->_ref_actes_caisse as $acte_caisse) {
          $coeff = "coeff_".$this->type_facture;
          $tarif_acte_caisse = ($acte_caisse->montant_base + $acte_caisse->montant_depassement)*$acte_caisse->_ref_caisse_maladie->$coeff;
          if ($acte_caisse->_ref_caisse_maladie->use_tarmed_bill) {
            $this->_autre_tarmed += $tarif_acte_caisse;
          }
          else {
            $this->_total_caisse +=  $tarif_acte_caisse;
          }
        }
      }
      parent::loadNumerosBVR();
    }
    return $this->_num_bvr;
  }
}
