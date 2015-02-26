<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Classe des protocoles
 */
class CProtocole extends CMbObject {
  // DB Table key
  public $protocole_id;

  // DB References
  public $chir_id;
  public $function_id;
  public $group_id;
  public $uf_hebergement_id; // UF de responsabilité d'hébergement
  public $uf_medicale_id;    // UF de responsabilité médicale
  public $uf_soins_id;       // UF de responsabilité de soins
  public $charge_id;

  // For sejour/intervention
  public $for_sejour; // Sejour / Operation

  // DB Fields Sejour
  public $type;
  public $DP;
  public $convalescence;
  public $rques_sejour; // Sejour->rques
  public $pathologie;
  public $septique;
  public $type_pec;
  public $facturable;

  // DB Fields Operation
  public $codes_ccam;
  public $libelle;
  public $cote;
  public $temp_operation;
  public $examen;
  public $materiel;
  public $exam_per_op;
  public $duree_hospi;
  public $duree_heure_hospi;
  public $rques_operation; // Operation->rques
  public $depassement;
  public $forfait;
  public $fournitures;
  public $service_id;
  public $libelle_sejour;
  public $duree_uscpo;
  public $duree_preop;
  public $presence_preop;
  public $presence_postop;
  public $exam_extempo;
  public $type_anesth;

  // DB fields linked protocols
  public $protocole_prescription_chir_id;
  public $protocole_prescription_chir_class;
  public $protocole_prescription_anesth_id;
  public $protocole_prescription_anesth_class;

  // Form fields
  public $_owner;
  public $_time_op;
  public $_codes_ccam = array();
  public $_types_ressources_ids;

  /** @var CMediusers */
  public $_ref_chir;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CGroups */
  public $_ref_group;

  /** @var CPrescription */
  public $_ref_protocole_prescription_chir;

  /** @var CPrescription */
  public $_ref_protocole_prescription_anesth;

  /** @var CUniteFonctionnelle */
  public $_ref_uf_hebergement;

  /** @var CUniteFonctionnelle */
  public $_ref_uf_medicale;

  /** @var CUniteFonctionnelle */
  public $_ref_uf_soins;

  // External references
  public $_ext_codes_ccam;
  public $_ext_code_cim;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'protocole';
    $spec->key   = 'protocole_id';
    $spec->xor["owner"] = array("chir_id", "function_id", "group_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["chir_id"]         = "ref class|CMediusers seekable";
    $props["function_id"]     = "ref class|CFunctions seekable";
    $props["group_id"]        = "ref class|CGroups seekable";
    $props["uf_hebergement_id"] = "ref class|CUniteFonctionnelle seekable";
    $props["uf_medicale_id"]    = "ref class|CUniteFonctionnelle seekable";
    $props["uf_soins_id"]       = "ref class|CUniteFonctionnelle seekable";
    $props["charge_id"]         = "ref class|CChargePriceIndicator autocomplete|libelle show|0";
    $props["for_sejour"]      = "bool notNull default|0";
    $props["type"]            = "enum list|comp|ambu|exte|seances|ssr|psy default|comp";
    $props["DP"]              = "code cim10";
    $props["convalescence"]   = "text";
    $props["rques_sejour"]    = "text";
    $props["libelle"]         = "str seekable";
    $props["cote"]            = "enum list|droit|gauche|haut|bas|bilatéral|total|inconnu";
    $props["libelle_sejour"]  = "str seekable";
    $props["service_id"]      = "ref".(CAppUI::conf("dPplanningOp CSejour service_id_notNull") == 1 ? ' notNull' : '')." class|CService seekable";
    $props["examen"]          = "text confidential seekable";
    $props["materiel"]        = "text confidential seekable";
    $props["exam_per_op"]     = "text confidential seekable";
    $props["duree_hospi"]     = "num notNull min|0 max|36500";
    $props["duree_heure_hospi"] = "num min|0 max|23 default|0";
    $props["rques_operation"] = "text confidential";
    $props["depassement"]     = "currency min|0 confidential";
    $props["forfait"]         = "currency min|0 confidential";
    $props["fournitures"]     = "currency min|0 confidential";
    $props["pathologie"]      = "str length|3";
    $props["septique"]        = "bool";
    $props["codes_ccam"]      = "str seekable";
    $props["temp_operation"]  = "time";
    $props["type_pec"]        = "enum list|M|C|O";
    $props["facturable"]      = "bool notNull default|1 show|0";
    $props["duree_uscpo"]     = "num min|0 default|0";
    $props["duree_preop"]     = "time show|0";
    $props["presence_preop"]  = "time show|0";
    $props["presence_postop"] = "time show|0";
    $props["exam_extempo"]    = "bool";
    $props["type_anesth"]     = "ref class|CTypeAnesth";

    $props["protocole_prescription_chir_id"]      = "ref class|CMbObject meta|protocole_prescription_chir_class";
    $props["protocole_prescription_chir_class"]   = "enum list|CPrescription|CPrescriptionProtocolePack";
    $props["protocole_prescription_anesth_id"]    = "ref class|CMbObject meta|protocole_prescription_anesth_class";
    $props["protocole_prescription_anesth_class"] = "enum list|CPrescription|CPrescriptionProtocolePack";

    $props["_time_op"]        = "time";
    $props["_owner"]          = "enum list|user|function|group";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["besoins_ressources"] = "CBesoinRessource protocole_id";
    $backProps["ufs"]  = "CAffectationUniteFonctionnelle object_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->codes_ccam = strtoupper($this->codes_ccam);
    if ($this->codes_ccam) {
      $this->_codes_ccam = explode("|", $this->codes_ccam);
    }
    else {
      $this->_codes_ccam = array();
    }

    $this->_time_op = $this->temp_operation;

    if ($this->libelle_sejour) {
      $this->_view = $this->libelle_sejour;
    }
    elseif ($this->libelle) {
      $this->_view = $this->libelle;
    }
    else {
      $this->_view = $this->codes_ccam;
    }

    if ($this->chir_id) {
      $this->_owner = "user";
    }
    if ($this->function_id) {
      $this->_owner = "function";
    }
    if ($this->group_id) {
      $this->_owner = "group";
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    if ($this->codes_ccam) {
      $this->codes_ccam = strtoupper($this->codes_ccam);
      $codes_ccam = explode("|", $this->codes_ccam);
      $XPosition = true;
      while ($XPosition !== false) {
        $XPosition = array_search("-", $codes_ccam);
        if ($XPosition !== false) {
          array_splice($codes_ccam, $XPosition, 1);
        }
      }
      $this->codes_ccam = implode("|", $codes_ccam);
    }
    if ($this->_time_op !== null) {
      $this->temp_operation = $this->_time_op;
    }
  }

  /**
   * @return CMediusers
   */
  function loadRefChir() {
    return $this->_ref_chir = $this->loadFwdRef("chir_id", true);
  }

  /**
   * @return CFunctions
   */
  function loadRefFunction() {
    return $this->_ref_function = $this->loadFwdRef("function_id", true);
  }

  /**
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * @return CPrescription
   */
  function loadRefPrescriptionChir() {
    return $this->_ref_protocole_prescription_chir = $this->loadFwdRef("protocole_prescription_chir_id", true);
  }

  /**
   * @return CPrescription
   */
  function loadRefPrescriptionAnesth() {
    return $this->_ref_protocole_prescription_anesth = $this->loadFwdRef("protocole_prescription_anesth_id", true);
  }

  function loadExtCodesCCAM() {
    $this->_ext_codes_ccam = array();
    foreach ($this->_codes_ccam as $code) {
      $this->_ext_codes_ccam[] = CDatedCodeCCAM::get($code);
    }
  }

  function loadExtCodeCIM() {
    $this->_ext_code_cim = CCodeCIM10::get($this->DP);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefChir();
    $this->loadRefFunction();
    $this->loadRefGroup();
    $this->loadRefPrescriptionChir();
    $this->loadRefPrescriptionAnesth();
    $this->loadExtCodesCCAM();
    $this->loadExtCodeCIM();
    $this->_view = "";
    if ($this->libelle_sejour) {
      $this->_view .= "$this->libelle_sejour";
    }
    elseif ($this->libelle) {
      $this->_view .= "$this->libelle";
    }
    else {
      foreach ($this->_ext_codes_ccam as $ccam) {
        $this->_view .= " - $ccam->code";
      }
    }
    if ($this->chir_id) {
      $this->_view .= " &mdash; Dr {$this->_ref_chir->_view}";
    }
    elseif ($this->function_id) {
      $this->_view .= " &mdash; Fonction {$this->_ref_function->_view}";
    }
    elseif ($this->chir_id) {
      $this->_view .= " &mdash; Etablissement {$this->_ref_group->_view}";
    }
  }

  function loadRefsBesoins() {
    return $this->_ref_besoins = $this->loadBackRefs("besoins_ressources");
  }

  function loadRefUFHebergement($cache = true) {
    return $this->_ref_uf_hebergement = $this->loadFwdRef("uf_hebergement_id", $cache);
  }

  function loadRefUFMedicale($cache = true) {
    return $this->_ref_uf_medicale = $this->loadFwdRef("uf_medicale_id", $cache);
  }

  function loadRefUFSoins($cache = true) {
    return $this->_ref_uf_soins = $this->loadFwdRef("uf_soins_id", $cache);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if ($this->chir_id) {
      if (!$this->_ref_chir) {
        $this->loadRefChir();
      }
      return $this->_ref_chir->getPerm($permType);
    }
    if ($this->function_id) {
      if (!$this->_ref_function) {
        $this->loadRefFunction();
      } 
      return $this->_ref_function->getPerm($permType);
    }
    if ($this->group_id) {
      if (!$this->_ref_group) {
        $this->loadRefGroup();
      } 
      return $this->_ref_group->getPerm($permType);
    }
  }

  /**
   * @see parent::store()
   */
  function store() {
    if (!$this->_id && $this->_types_ressources_ids) {
      if ($msg = parent::store()) {
        return $msg;
      }

      $types_ressources_ids = explode(",", $this->_types_ressources_ids);

      foreach ($types_ressources_ids as $_type_ressource_id) {
        $besoin = new CBesoinRessource;
        $besoin->type_ressource_id = $_type_ressource_id;
        $besoin->protocole_id = $this->_id;
        if ($msg = $besoin->store()) {
          return $msg;
        }
      }
    }

    return parent::store();
  }
}
