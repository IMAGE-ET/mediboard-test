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
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_cabinet';
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
    $backProps["consultations"] = "CConsultation facture_id";
    $backProps["reglements"]    = "CReglement object_id";
    $backProps["relance_fact_cabinet"] = "CRelance object_id";
    return $backProps;
  }
   
  /**
   * updateFormFields
   * 
   * @return void
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = sprintf("FA%08d", $this->_id);
  }

  /**
   * Chargement des règlements de la facture
   * 
   * @param bool $cache cache
   * 
   * @return $this->_ref_reglements
  **/
  function loadRefsReglements($cache = 1) {
    $this->_ref_reglements = $this->loadBackRefs("reglements", 'date');
    return parent::loadRefsReglements($cache);
  }
  
  /**
   * Redéfinition du store
   * 
   * @return void
  **/
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
    $this->loadRefsReglements();
    $this->loadRefsRelances();
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }
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
   * @return $facture
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
   * @return _ref_relances
  **/
  function loadRefsRelances(){
    $this->_ref_relances = $this->loadBackRefs("relance_fact_cabinet", 'date');
    $this->IsRelancable();
    return $this->_ref_relances;
  }

  /**
   * Fonction permettant l'ajout d'une consultation à une facture
   * 
   * @param string $patient_id   le patient
   * @param string $chirsel_id   le praticien
   * @param string $consult_id   l'id de la consultation
   * @param string $type_facture le type de la facture
   * 
   * @return void
  **/
  function ajoutConsult($patient_id, $chirsel_id, $consult_id, $type_facture) {
    //@todo à déplacer dans le store de la facture/consult
    $this->_consult_id = $consult_id;
    // Si la facture existe déjà on la met à jour
    $where = array();
    $ljoin = array();
    if (CAppUI::conf("ref_pays") == 2) {
      $where["patient_id"]    = "= '$patient_id'";
      $where["praticien_id"]  = "= '$chirsel_id'";
      $where["cloture"]       = "IS NULL";
    }
    else {
      $ljoin["facture_liaison"] =  "facture_liaison.facture_id = facture_cabinet.facture_id";
      $where["facture_liaison.object_id"]     = " = '$this->_consult_id'";
      $where["facture_liaison.object_class"]  = " = 'CConsultation'";
      $where["facture_liaison.facture_class"] = " = 'CFactureCabinet'";
    }
    
    //Si la facture existe déjà
    if ($this->loadObject($where, null, null, $ljoin)) {
      //Dans le cas Suisse
      if (CAppUI::conf("ref_pays") == 2) {
        if (CModule::getActive("dPfacturation")) {
          $ligne = new CFactureLiaison();
          $ligne->facture_id    = $this->_id;
          $ligne->facture_class = $this->_class;
          $ligne->object_id     = $consult_id;
          $ligne->object_class  = 'CConsultation';
          if (!$ligne->loadMatchingObject()) {
            $ligne->store();
          }
        }
      }
    }
    else {
      // Sinon on la crée
      $consult = new CConsultation();
      $consult->load($consult_id);
      $this->ouverture    = CMbDT::date();
      $this->patient_id   = $patient_id;
      $this->praticien_id = $chirsel_id;
      $this->type_facture = $type_facture;
      $this->du_patient   = $consult->du_patient;
      $this->du_tiers     = $consult->du_tiers;
      if (CAppUI::conf("ref_pays") == 1) {
        $this->cloture    = CMbDT::date();
      }
      $this->store();
      
      if (CModule::getActive("dPfacturation")) {
        $ligne = new CFactureLiaison();
        $ligne->facture_id    = $this->_id;
        $ligne->facture_class = $this->_class;
        $ligne->object_id     = $consult_id;
        $ligne->object_class  = 'CConsultation';
        $ligne->store();
      }
      else {
        $consult->facture_id = $this->_id;
        $consult->store();
      }
    }
  }
}
