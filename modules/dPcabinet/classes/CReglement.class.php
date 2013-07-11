<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */


/**
 * Les règlements
 */
class CReglement extends CMbMetaObject {
  // DB Table key
  public $reglement_id;

  // DB References
  public $banque_id;

  // DB fields
  public $date;
  public $montant;
  public $emetteur;
  public $mode;
  public $object_class;
  public $object_id;
  public $reference;
  public $num_bvr;
  public $tireur;
  public $debiteur_id;
  public $debiteur_desc;

  // Behaviour fields
  public $_update_facture = true;

  // References
  /** @var CBanque */
  public $_ref_banque;
  /** @var CFacture */
  public $_ref_facture;
  /** @var CDebiteur */
  public $_ref_debiteur;
  

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'reglement';
    $spec->key   = 'reglement_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['object_class']    = 'enum notNull list|CFactureCabinet|CFactureEtablissement show|0 default|CFactureCabinet';
    $props['banque_id']       = 'ref class|CBanque';
    $props['date']            = 'dateTime notNull';
    $props['montant']         = 'currency notNull';
    $props['emetteur']        = 'enum notNull list|patient|tiers';
    $props['mode']            = 'enum notNull list|cheque|CB|especes|virement|BVR|autre default|cheque';
    $props['reference']       = 'str';
    $props['num_bvr']         = 'str maxLength|50';
    $props['tireur']          = 'str';
    $props["debiteur_id"]     = "ref class|CDebiteur";
    $props["debiteur_desc"]   = "str";
    return $props;
  }
  
  /**
   * Charge la banque
   * 
   * @return CBanque La banque
   */
  function loadRefBanque() {
    return $this->_ref_banque = $this->loadFwdRef("banque_id", true);
  }

  /**
   * Charge le debiteur s'il existe
   *
   * @return CDebiteur le débiteur
   */
  function loadRefDebiteur() {
    return $this->_ref_debiteur = $this->loadFwdRef("debiteur_id", true);
  }
  
  /**
   * @see parent::loadRefsFwd()
   * @deprecated
   */
  function loadRefsFwd() {
    $this->loadTargetObject();
    $this->loadRefBanque();
  }
  
  /**
   * @see parent::check()
   */
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    $this->completeField("montant");
    if (!$this->montant) {
      return 'Le montant du règlement ne doit pas être nul';
    }

    $this->loadRefsFwd();
    return null;
  }
  
  /**
   * Accesseur sur la facture
   * 
   * @return CFacture
   */
  function loadRefFacture() {
    /** @var CFacture $facture */
    $facture = $this->loadTargetObject();
    $facture->loadRefsObjects();
    $facture->loadRefPatient();
    $facture->loadRefPraticien();
    return $this->_ref_facture = $facture;
  }
  
  /**
   * Acquite la facture automatiquement
   * 
   * @return string|null
   */
  function acquiteFacture() {
    $this->loadRefBanque();
    $facture = $this->loadRefFacture();
    $facture->loadRefsReglements();
    
    // Acquitement patient
    if ($this->emetteur == "patient" && $facture->du_patient) {
      $facture->patient_date_reglement = $facture->_du_restant_patient <= 0 ? CMbDT::date() : "";
    }
    
    // Acquitement tiers
    if ($this->emetteur == "tiers" && $facture->du_tiers) {
      $facture->tiers_date_reglement = $facture->_du_restant_tiers <= 0 ? CMbDT::date() : "";
    }
    
    return $facture->store();
  }
  
  /**
   * Redéfinition du store
   * 
   * @return string|null
   */
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }

    return $this->acquiteFacture();
  }
  
  /**
   * Redéfinition du delete
   * 
   * @return string|null
   */
  function delete() {
    // Preload consultation
    $this->load();
    $this->loadRefsFwd();
    
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }

    return $this->acquiteFacture();
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    return $this->loadTargetObject()->getPerm($permType);
  }
}
