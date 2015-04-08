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
 * Permet d'editer des relances pour les factures impayees
 */
class CRelance extends CMbMetaObject {
  // DB Table key
  public $relance_id;
  
  // DB Fields
  public $object_id;
  public $object_class;
  public $date;
  public $etat;
  public $du_patient;
  public $du_tiers;
  public $numero;
  public $statut;
  public $poursuite;
  public $facture;
  public $envoi_xml;
  public $request_date;

  public $_montant;
  // Object References
  /** @var  CFactureCabinet|CFactureEtablissement $_ref_object*/
  public $_ref_object;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_relance';
    $spec->key   = 'relance_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_class"]  = "enum notNull list|CFactureCabinet|CFactureEtablissement default|CFactureCabinet";
    $props["date"]          = "date";
    $props["etat"]          = "enum notNull list|emise|regle|renouvelle default|emise";
    $props["numero"]        = "num notNull min|1 max|10 default|1";
    $props["du_patient"]    = "currency decimals|2";
    $props["du_tiers"]      = "currency decimals|2";
    $props["statut"]        = "enum list|inactive|first|second|third|contentieux|poursuite";
    $props["poursuite"]     = "enum list|defaut|continuation|etranger|faillite|hors_pays|deces|inactive|saisie|introuvable";
    $props["envoi_xml"]     = "bool default|1";
    $props["facture"]       = "enum notNull list|-1|0|1 default|0";
    $props["request_date"]  = "dateTime";

    $props["_montant"]      = "currency decimals|2";
    return $props;
  }
  
  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Relance du ".$this->date;
    $this->_montant = $this->du_patient + $this->du_tiers;
  }
  
  /**
   * Chargement de l'objet facturable
   * 
   * @return CFacture
  **/
  function loadRefFacture() {
    return $this->_ref_object =  $this->loadTargetObject();
  }

  /**
   * Redefinition du store
   * 
   * @return void|string
  **/
  function store() {
    if (!$this->_id && $this->object_class && $this->object_id) {
      $this->_ref_object = new $this->object_class;
      $this->_ref_object->load($this->object_id);
      $this->_ref_object->loadRefPatient();
      $this->_ref_object->loadRefPraticien();
      $this->_ref_object->loadRefsObjects();
      $this->_ref_object->loadRefsReglements();
      $this->_ref_object->loadRefsRelances();
  
      $this->date       = CMbDT::date();
      $this->du_patient = $this->_ref_object->_du_restant_patient + $this->_ref_object->_reglements_total_patient;
      $this->du_tiers   = $this->_ref_object->_du_restant_tiers + $this->_ref_object->_reglements_total_tiers;
      $der_relance      = $this->_ref_object->_ref_last_relance;
      if ($der_relance->_id) {
        if ($der_relance->statut == "inactive") {
          return "La derniere relance est inactive";
        }
        if ($der_relance->etat != "regle") {
          $this->numero = $der_relance->numero + 1;
          $der_relance->etat = "renouvelle";
          $der_relance->store();
        }
        else {
          return "La derniere relance est reglee";
        }
      }
      if (!$this->numero) {
        $this->numero = 1;
      }
      switch ($this->numero) {
        case "1":
          $this->du_patient += CAppUI::conf("dPfacturation CRelance add_first_relance");
          $this->statut = "first";
          break;
        case "2":
          $this->du_patient += CAppUI::conf("dPfacturation CRelance add_second_relance");
          $this->statut = "second";
          break;
        case "3":
          $this->du_patient += CAppUI::conf("dPfacturation CRelance add_third_relance");
          $this->statut = "third";
          break;
      }
    }
    
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
  }

  /**
   * Redefinition du delete
   * 
   * @return void|string
  **/
  function delete() {
    //Supression possible que de la derniere relance d'une facture
    /** @var  CFactureCabinet|CFactureEtablissement $facture*/
    $facture = $this->loadRefFacture();
    $facture->loadRefsRelances();
    if (count($facture->_ref_relances) > 1 && $this->_id != $facture->_ref_last_relance->_id) {
      return "Vous ne pouvez supprimer que la derniere relance emise";
    }
    
    //Une relance reglee, ne peut pas etre supprimee
    if ($this->etat == "regle") {
      return "La relance est reglee, vous ne pouvez pas la supprimer"; 
    }
    
    // Standard store
    if ($msg = parent::delete()) {
      return $msg;
    }
    
    $facture->loadRefsRelances();
    $facture->_ref_last_relance->etat = "emise";
    $facture->_ref_last_relance->store();
  }
}
