<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

class CProtocole extends CMbObject {
  // DB Table key
  var $protocole_id = null;

  // DB References
  var $chir_id     = null;
  var $function_id = null;
  var $group_id    = null;

  // For sejour/intervention
  var $for_sejour = null; // Sejour / Operation
  
  // DB Fields Sejour
  var $type          = null;
  var $DP            = null;
  var $convalescence = null;
  var $rques_sejour  = null; // Sejour->rques
  var $pathologie    = null;
  var $septique      = null;
  var $type_pec      = null;
  
  // DB Fields Operation
  var $codes_ccam        = null;
  var $libelle           = null;
  var $cote              = null;
  var $temp_operation    = null;
  var $examen            = null;
  var $materiel          = null;
  var $duree_hospi       = null;
  var $rques_operation   = null; // Operation->rques
  var $depassement       = null;
  var $forfait           = null;
  var $fournitures       = null;
  var $service_id        = null;
  var $libelle_sejour    = null;
  var $duree_uscpo       = null;
  var $duree_preop       = null;
  var $presence_preop    = null;
  var $presence_postop   = null;
  
  // DB fields linked protocols
  var $protocole_prescription_chir_id      = null;
  var $protocole_prescription_chir_class   = null;
  var $protocole_prescription_anesth_id    = null;
  var $protocole_prescription_anesth_class = null;

  // Form fields
  var $_hour_op        = null;
  var $_min_op         = null;
  var $_codes_ccam     = array();

  // DB References
  var $_ref_chir                          = null;
  var $_ref_function                      = null;
  var $_ref_group                         = null;
  var $_ref_protocole_prescription_chir   = null;
  var $_ref_protocole_prescription_anesth = null;

  // External references
  var $_ext_codes_ccam = null;
  var $_ext_code_cim   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'protocole';
    $spec->key   = 'protocole_id';
    $spec->xor["owner"] = array("chir_id", "function_id", "group_id");
    return $spec;
  }

  function getProps() {
  	$props = parent::getProps();
    $props["chir_id"]         = "ref class|CMediusers seekable";
    $props["function_id"]     = "ref class|CFunctions seekable";
    $props["group_id"]        = "ref class|CGroups seekable";
    $props["for_sejour"]      = "bool notNull default|0";
    $props["type"]            = "enum list|comp|ambu|exte|seances|ssr|psy default|comp";
    $props["DP"]              = "code cim10";
    $props["convalescence"]   = "text";
    $props["rques_sejour"]    = "text";
    $props["libelle"]         = "str seekable";
    $props["cote"]            = "enum list|droit|gauche|bilatéral|total|inconnu";
    $props["libelle_sejour"]  = "str seekable";
    $props["service_id"]      = "ref".(CAppUI::conf("dPplanningOp CSejour service_id_notNull") == 1 ? ' notNull' : '')." class|CService seekable";
    $props["examen"]          = "text confidential seekable";
    $props["materiel"]        = "text confidential seekable";
    $props["duree_hospi"]     = "num notNull min|0 max|36500";
    $props["rques_operation"] = "text confidential";
    $props["depassement"]     = "currency min|0 confidential";
    $props["forfait"]         = "currency min|0 confidential";
    $props["fournitures"]     = "currency min|0 confidential";
    $props["pathologie"]      = "str length|3";
    $props["septique"]        = "bool";
    $props["codes_ccam"]      = "str seekable";
    $props["temp_operation"]  = "time";
    $props["type_pec"]        = "enum list|M|C|O";
    $props["duree_uscpo"]     = "num min|0 default|0";
    $props["duree_preop"]     = "time show|0";
    $props["presence_preop"]  = "time show|0";
    $props["presence_postop"] = "time show|0";
    
    $props["protocole_prescription_chir_id"]      = "ref class|CMbObject meta|protocole_prescription_chir_class";
    $props["protocole_prescription_chir_class"]   = "enum list|CPrescription|CPrescriptionProtocolePack";
    $props["protocole_prescription_anesth_id"]    = "ref class|CMbObject meta|protocole_prescription_anesth_class";
    $props["protocole_prescription_anesth_class"] = "enum list|CPrescription|CPrescriptionProtocolePack";
    
    $props["_hour_op"]        = "num";
    $props["_min_op"]         = "num";
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->codes_ccam = strtoupper($this->codes_ccam);
    if ($this->codes_ccam) {
      $this->_codes_ccam = explode("|", $this->codes_ccam);
    } 
    else {
      $this->_codes_ccam = array();
    }

    $this->_hour_op = intval(substr($this->temp_operation, 0, 2));
    $this->_min_op  = intval(substr($this->temp_operation, 3, 2));
    
    if ($this->libelle_sejour) {
      $this->_view = $this->libelle_sejour;
    } 
    elseif ($this->libelle) {
      $this->_view = $this->libelle;
    } 
    else {
      $this->_view = $this->codes_ccam;
    }
  }

  function updatePlainFields() {
    if($this->codes_ccam) {
      $this->codes_ccam = strtoupper($this->codes_ccam);
      $codes_ccam = explode("|", $this->codes_ccam);
      $XPosition = true;
      while($XPosition !== false) {
        $XPosition = array_search("-", $codes_ccam);
        if ($XPosition !== false) {
          array_splice($codes_ccam, $XPosition, 1);
        }
      }
      $this->codes_ccam = implode("|", $codes_ccam);
    }
    if ($this->_hour_op !== null and $this->_min_op !== null) {
      $this->temp_operation =
        $this->_hour_op.":".
        $this->_min_op.":00";
    }
  }

  function loadRefChir() {
    $this->_ref_chir = $this->loadFwdRef("chir_id", true);
    return $this->_ref_chir;
  }

  function loadRefFunction() {
    $this->_ref_function = $this->loadFwdRef("function_id", true);
    return $this->_ref_function;
  }

  function loadRefGroup() {
    $this->_ref_group = $this->loadFwdRef("group_id", true);
    return $this->_ref_group;
  }

  function loadRefPrescriptionChir() {
    if (!$this->protocole_prescription_chir_class && !$this->protocole_prescription_chir_id) {
      return $this->_ref_protocole_prescription_chir = new CPrescription;
    }
    $this->_ref_protocole_prescription_chir = new $this->protocole_prescription_chir_class;
    return $this->_ref_protocole_prescription_chir->load($this->protocole_prescription_chir_id);
  }

  function loadRefPrescriptionAnesth() {
    if (!$this->protocole_prescription_anesth_class && !$this->protocole_prescription_anesth_id) {
      return $this->_ref_protocole_prescription_anesth = new CPrescription;
    }
    $this->_ref_protocole_prescription_anesth = new $this->protocole_prescription_anesth_class;
    return $this->_ref_protocole_prescription_anesth->load($this->protocole_prescription_anesth_id);
  }

  function loadExtCodesCCAM() {
    $this->_ext_codes_ccam = array();
    foreach ($this->_codes_ccam as $code) {
      $this->_ext_codes_ccam[] = CCodeCCAM::get($code, CCodeCCAM::LITE);
    }
  }

  function loadExtCodeCIM() {
    $this->_ext_code_cim = new CCodeCIM10($this->DP);
    $this->_ext_code_cim->loadLite();
  }

  function loadRefsFwd() {
    $this->loadRefChir();
    $this->loadRefFunction();
    $this->loadRefGroup();
    $this->loadRefPrescriptionChir();
    $this->loadRefPrescriptionAnesth();
    $this->loadExtCodesCCAM();
    $this->loadExtCodeCIM();
    $this->_view = "";
    if($this->libelle_sejour) {
      $this->_view .= "$this->libelle_sejour";
    } elseif($this->libelle) {
      $this->_view .= "$this->libelle";
    } else {
      foreach($this->_ext_codes_ccam as $key => $ccam) {
        $this->_view .= " - $ccam->code";
      }
    }
    if($this->chir_id) {
      $this->_view .= " &mdash; Dr {$this->_ref_chir->_view}";
    }else if($this->function_id) {
      $this->_view .= " &mdash; Fonction {$this->_ref_function->_view}";
    }else if($this->chir_id) {
      $this->_view .= " &mdash; Etablissement {$this->_ref_group->_view}";
    }
  }

  function getPerm($permType) {
    if($this->chir_id) {
      if(!$this->_ref_chir) {
        $this->loadRefChir();
      } 
      return $this->_ref_chir->getPerm($permType);
    }
    if($this->function_id) {
      if(!$this->_ref_function) {
        $this->loadRefFunction();
      } 
      return $this->_ref_function->getPerm($permType);
    }
    if($this->group_id) {
      if(!$this->_ref_group) {
        $this->loadRefGroup();
      } 
      return $this->_ref_group->getPerm($permType);
    }
  }
}

?>