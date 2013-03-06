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
  
  // Fwd References
  public $_ref_consultation;
  public $_ref_banque;
  public $_ref_facture;
  
  var $_update_facture = true;
  
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'reglement';
    $spec->key   = 'reglement_id';
    return $spec;
  }
  
  /**
   * getProps
   * 
   * @return $props
  **/
  function getProps() {
    $specs = parent::getProps();
    $specs['object_class']    = 'enum notNull list|CFactureCabinet|CFactureEtablissement show|0 default|CFactureCabinet';
    $specs['banque_id']       = 'ref class|CBanque';
    $specs['date']            = 'dateTime notNull';
    $specs['montant']         = 'currency notNull';
    $specs['emetteur']        = 'enum notNull list|patient|tiers';
    $specs['mode']            = 'enum notNull list|cheque|CB|especes|virement|BVR|autre default|cheque';
    $specs['reference']       = 'str';
    $specs['num_bvr']         = 'str';
    return $specs;
  }
  
  /**
   * Accesseur sur la banque
   * 
   * @return array La banque
   */
  function loadRefBanque() {
    $this->_ref_banque = $this->loadFwdRef("banque_id", true);
  }
  
  /**
   * loadRefsFwd
   * 
   * @return void
  **/
  function loadRefsFwd() {
    $this->loadTargetObject();
    $this->loadRefBanque();
  }
  
  /**
   * Vérification des champs
   * 
   * @return void
  **/
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    $this->completeField("montant", "mode");
    
    if (!$this->montant) {
      return 'Le montant du règlement ne doit pas être nul';
    }
    
    if (!$this->mode) {
      return 'Le mode de paiment ne doit pas être nul';
    }
    $this->loadRefsFwd();
  }
  
  /**
   * Accesseur sur la facture
   * 
   * @return array La facture
   */
  function loadRefFacture() {
    $target = $this->loadTargetObject();
    $target->loadRefsObjects();
    $target->loadRefPatient();
    $target->loadRefPraticien();
    return $this->_ref_facture = $target;
  }
  
  /**
   * Acquite la facture automatiquement
   * 
   * @return Store-like message
   */
  function acquiteFacture() {
    $this->loadRefsFwd();
    $facture = $this->_ref_object;
    $facture->loadRefsObjects();
    $facture->loadRefsReglements();
    
    // Acquitement patient
    if ($this->emetteur == "patient" && $facture->du_patient) {
      $facture->patient_date_reglement = $facture->_du_restant_patient <= 0 ? mbDate() : "";
    }
    
    // Acquitement tiers
    if ($this->emetteur == "tiers" && $facture->du_tiers) {
      $facture->tiers_date_reglement = $facture->_du_restant_tiers <= 0 ? mbDate() : "";
    }
    
    return $facture->store();
  }
  
  /**
   * Redéfinition du store
   * 
   * @return void
  **/
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
   * @return void
  **/
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
  
  function getPerm($permType) {
    return $this->loadTargetObject()->getPerm($permType);
  }
}

?>