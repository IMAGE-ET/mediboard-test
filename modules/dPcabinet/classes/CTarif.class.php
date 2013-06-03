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
 * Tarif
 */
class CTarif extends CMbObject {
  // DB Table key
  public $tarif_id;

  // DB References
  public $chir_id;
  public $function_id;
  public $group_id;

  // DB fields
  public $description;
  public $secteur1;
  public $secteur2;
  public $codes_ccam;
  public $codes_ngap;
  public $codes_tarmed;
  public $codes_caisse;
  
  // Form fields
  public $_type;
  public $_somme;
  public $_codes_ngap = array();
  public $_codes_ccam = array();
  public $_codes_tarmed = array();
  public $_codes_caisse = array();
  public $_new_actes  = array();
  
  // Remote fields
  public $_precode_ready;
  public $_secteur1_uptodate;
  public $_has_mto;
  
  // Behaviour fields
  public $_add_mto;
  public $_add_code;
  public $_dell_code;
  public $_code;
  public $_code_ref;
  public $_quantite;
  public $_type_code;
  public $_update_montants;
  
  // Object References
  public $_ref_chir;
  public $_ref_function;
  public $_ref_group;
  
  public $_bind_codable;
  public $_codable_class;
  public $_codable_id;
  
  /**
   * getSpec
   * 
   * @return CMbObjectSpec the spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'tarifs';
    $spec->key   = 'tarif_id';
    //$spec->xor["owner"] = array("chir_id", "function_id", "group_id");
    return $spec;
  }
  
  /**
   * getProps
   * 
   * @return array
  **/
  function getProps() {
    $props = parent::getProps();
    $props["chir_id"]     = "ref class|CMediusers";
    $props["function_id"] = "ref class|CFunctions";
    $props["group_id"]    = "ref class|CGroups";
    $props["description"] = "str notNull confidential seekable";
    $props["secteur1"]    = "currency notNull min|0";
    $props["secteur2"]    = "currency";
    $props["codes_ccam"]  = "str";
    $props["codes_ngap"]  = "str";
    $props["codes_tarmed"]= "str";
    $props["codes_caisse"]= "str";
    $props["_somme"]      = "currency";
    $props["_type"]       = "";
    
    $props["_precode_ready"] = "bool";
    $props["_has_mto"]       = "bool";
    
    return $props;
  }
  
  /**
   * updateFormFields
   * 
   * @return void
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->description;
    if ($this->chir_id) {
      $this->_type = "chir";
    }
    elseif ($this->function_id) {
      $this->_type = "function";
    }
    else {
      $this->_type = "group";
    }
    $this->_codes_ngap = explode("|", $this->codes_ngap);
    $this->_codes_ccam = explode("|", $this->codes_ccam);
    $this->_codes_tarmed = explode("|", $this->codes_tarmed);
    $this->_codes_caisse = explode("|", $this->codes_caisse);
    CMbArray::removeValue("", $this->_codes_ngap);
    CMbArray::removeValue("", $this->_codes_ccam);
    CMbArray::removeValue("", $this->_codes_tarmed);
    CMbArray::removeValue("", $this->_codes_caisse);
    $this->_somme = $this->secteur1 + $this->secteur2;
  }
  
  /**
   * updatePlainFields
   * 
   * @return void
  **/
  function updatePlainFields() {
    if ($this->_type !== null) {
      if ($this->_type == "chir") {
        $this->function_id = "";
        $this->group_id = "";
      }
      if ($this->_type == "function") {
        $this->chir_id = "";
        $this->group_id = "";
      }
      if ($this->_type == "group") {
        $this->function_id = "";
        $this->chir_id = "";
      }
    }

    $this->updateMontants();
    $this->bindCodable();
  }
  
  /**
   * Chargement de la consultation associée
   * 
   * @return void
  **/
  function bindCodable() {
    if (!$this->_bind_codable || is_null($this->_codable_class) || is_null($this->_codable_id)) {
      return;
    }

    $this->_bind_codable = false;

    // Chargement de la consultation
    $codable = new $this->_codable_class();
    $codable->load($this->_codable_id);

    $codable->loadRefsActes();
    $codable->loadRefPraticien();

    // Affectation des valeurs au tarif
    $this->codes_ccam  = $codable->_tokens_ccam;
    $this->codes_ngap  = $codable->_tokens_ngap;
    $this->codes_tarmed= $codable->_tokens_tarmed;
    $this->codes_caisse= $codable->_tokens_caisse;
    $this->chir_id     = $codable->_ref_praticien->_id;
    $this->function_id = "";

    if ($this->_codable_class == "CConsultation") {
      $codable->loadRefPlageConsult();
      $this->secteur1    = $codable->secteur1;
      $this->secteur2    = $codable->secteur2;
      $this->description = $codable->tarif;
    }
  }
  
  /**
   * Redéfinition du store
   * 
   * @return null
  **/
  function store() {
    if ($this->_add_mto) {
      $this->completeField("codes_ngap");
      $this->codes_ngap .= "|1-MTO-1---0-";
    }
    
    if ($this->_add_code || $this->_dell_code) {
      $this->modifActes();
    }
    
    return parent::store();
  }
  
  /**
   * Mise à jour du montant du tarif
   * 
   * @return integer|null
  **/
  function updateMontants() {
    if (!$this->_update_montants) {
      return null;
    }

    $this->secteur1 = 0.00;
    $secteur2 = $this->secteur2;
  
    $tab = array( "codes_ccam" => "CActeCCAM",
                  "codes_ngap" => "CActeNGAP");
    if (CModule::getActive("tarmed")) {
      $tab["codes_tarmed"] = "CActeTarmed";
      $tab["codes_caisse"] = "CActeCaisse";
    }
    
    foreach ($tab as $codes => $class_acte) {
      $_codes = "_".$codes;
      $this->completeField($codes);
      $this->$_codes = explode("|", $this->$codes);
      CMbArray::removeValue("", $this->$_codes);
      foreach ($this->$_codes as &$_code) {
        $acte = new $class_acte;
        $acte->setFullCode($_code);
        $this->secteur1 += $acte->updateMontantBase();
        
         // Affectation du secteur 2 au dépassement du premier acte trouvé
        $acte->montant_depassement = $secteur2 ? $secteur2 : 0;
        $secteur2 = 0;
        
        $_code = $acte->makeFullCode();
      }
      $this->$codes = implode("|", $this->$_codes);
    }
    
    return $this->secteur1;
  }
  
  /**
   * Chargement du secteur 1 du tarif
   * 
   * @return $this->_secteur1_uptodate
  **/
  function getSecteur1Uptodate() {
    if ((!$this->codes_ngap && !$this->codes_ccam) || (!$this->codes_tarmed && !$this->codes_caisse)) {
      return $this->_secteur1_uptodate = "1";
    }
    
    // Backup ...
    $secteur1   = $this->secteur1;
    $codes_ccam = $this->_codes_ccam;
    $codes_ngap = $this->_codes_ngap;
    $codes_tarmed = $this->_codes_tarmed;
    $codes_caisse = $this->_codes_caisse;
    
    // Compute...
    $this->_update_montants = true;
    $new_secteur1 = $this->updateMontants();
    
    // ... and restore
    $this->secteur1 = $secteur1;
    $this->_codes_ccam = $codes_ccam;
    $this->_codes_ngap = $codes_ngap;
    $this->_codes_tarmed = $codes_tarmed;
    $this->_codes_caisse = $codes_caisse;

    return $this->_secteur1_uptodate = CFloatSpec::equals($secteur1, $new_secteur1, $this->_specs["secteur1"]) ? "1" : "0";
  }
  
  /**
   * Precodage des tarifs
   * 
   * @return string
  **/
  function getPrecodeReady() {
    $this->_has_mto = '0';
    $this->_new_actes = array();
    
    if (count($this->_codes_ccam) + count($this->_codes_ngap) + count($this->_codes_tarmed) + count($this->_codes_caisse) == 0) {
      return $this->_precode_ready = '0';
    }
    
    $tab = array( "_codes_ccam" => "CActeCCAM",
                  "_codes_ngap" => "CActeNGAP");
    if (CModule::getActive("tarmed")) {
      $tab["_codes_tarmed"] = "CActeTarmed";
      $tab["_codes_caisse"] = "CActeCaisse";
    }

    foreach ($tab as $codes => $class_acte) {
      foreach ($this->$codes as $code) {
        $acte = new $class_acte;
        $acte->setFullCode($code);
        if ($class_acte == "CActeTarmed") {
          $acte->loadRefTarmed(CTarmed::LITE);
        }
        elseif ($class_acte == "CActeCaisse") {
          $acte->loadRefPrestationCaisse();
        }
        $this->_new_actes[$code] = $acte;
        if (!$acte->getPrecodeReady()) {
          return $this->_precode_ready = '0';
        }
        if ($class_acte == "CActeNGAP" && in_array($acte->code, array("MTO", "MPJ"))) {
          $this->_has_mto = '1';
        }
      }
    }
    
    return $this->_precode_ready = '1';
  }
  
  /**
   * loadRefsFwd
   * 
   * @return void
  **/
  function loadRefsFwd() {
    $this->_ref_chir     = $this->loadFwdRef("chir_id");
    $this->_ref_function = $this->loadFwdRef("function_id");
    $this->loadRefGroup();
    $this->getPrecodeReady();
  }
  
  /**
   * Charge les permissions
   * 
   * @param string $permType Type de la permission
   * 
   * @return bool
   */
  function getPerm($permType) {
    if (!$this->_ref_chir || !$this->_ref_function) {
      $this->loadRefsFwd();
    }
    
    return 
      $this->_ref_chir->getPerm($permType) || 
      $this->_ref_function->getPerm($permType);
  }
  
  /**
   * Charge l'établissement associé au tarif
   * 
   * @param boolean $cached Charge l'établissement depuis le cache
   * 
   * @return CGroups
   */
  function loadRefGroup($cached = true) {
    return $this->_ref_group = $this->loadFwdRef("group_id", $cached);
  }
  
  /**
   * Permet d'ajouter ou supprimer un code au tarif
   * 
   * @return void
   */
  function modifActes() {
    $tab_classes = array(
      "tarmed" => "CActeTarmed",
      "caisse" => "CActeCaisse");
    $class_acte = $tab_classes[$this->_type_code]; 
    
    $codes  = "codes_".$this->_type_code;
    $_codes = "_codes_".$this->_type_code;
    
    $this->completeField($codes);
    $this->$_codes = explode("|", $this->$codes);
    CMbArray::removeValue("", $this->$_codes);
    foreach ($this->$_codes as &$_code) {
      $acte = new $class_acte;
      $acte->setFullCode($_code);
      $acte->updateMontantBase();  
      $acte->makeFullCode();
      $_code = $this->_dell_code && $this->_code == $acte->code ? "" : $acte->_full_code;
    }
    if ($this->_add_code) {
      $acte = new $class_acte;
      $acte->code = $this->_code;
      if ($class_acte == "CActeTarmed") {
        $acte->code_ref = $this->_code_ref;
      }
      $acte->quantite = $this->_quantite;
      $acte->updateMontantBase();
      if ($acte->montant_base) {
        array_push($this->$_codes, $acte->makeFullCode());
      }
    }
    $this->$codes = implode("|", $this->$_codes);
    
    // Recalcul des totaux du tarif
    $this->_update_montants = true;
    $this->updateMontants();
  }

  /**
   * Permet le chargement des actes du tarif
   *
   * @return void
   */
  function loadActes() {
    $tab = array( "codes_ccam" => "CActeCCAM",
                  "codes_ngap" => "CActeNGAP");
    if (CModule::getActive("tarmed")) {
      $tab["codes_tarmed"] = "CActeTarmed";
      $tab["codes_caisse"] = "CActeCaisse";
    }
    foreach ($tab as $codes => $class_acte) {
      $_codes = "_".$codes;
      $this->completeField($codes);
      $this->$_codes = explode("|", $this->$codes);
      CMbArray::removeValue("", $this->$_codes);
      foreach ($this->$_codes as &$_code) {
        $acte = new $class_acte;
        $acte->setFullCode($_code);
        $acte->updateMontantBase();
        $_code = $acte;
      }
    }
  }
}