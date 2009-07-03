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
  var $chir_id = null; // Sejour / Operation

  // For sejour/intervention
  var $for_sejour = null;
  
  // DB Fields Sejour
  var $type          = null;
  var $DP            = null;
  var $convalescence = null;
  var $rques_sejour  = null; // Sejour->rques
  var $pathologie    = null;
  var $septique      = null;

  // DB Fields Operation
  var $codes_ccam      = null;
  var $libelle         = null;
  var $temp_operation  = null;
  var $examen          = null;
  var $materiel        = null;
  var $duree_hospi     = null;
  var $rques_operation = null; // Operation->rques
  var $depassement     = null;
  var $forfait         = null;
  var $fournitures     = null;
  var $service_id_sejour = null;
  var $libelle_sejour  = null;

  // DB fields linked protocols
  var $protocole_prescription_chir_id   = null;
  var $protocole_prescription_anesth_id = null;

  // Form fields
  var $_hour_op        = null;
  var $_min_op         = null;
  var $_codes_ccam     = array();

  // DB References
  var $_ref_chir                          = null;
  var $_ref_protocole_prescription_chir   = null;
  var $_ref_protocole_prescription_anesth = null;

  // External references
  var $_ext_codes_ccam = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'protocole';
    $spec->key   = 'protocole_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["chir_id"]         = "ref notNull class|CMediusers seekable";
    $specs["for_sejour"]      = "bool notNull default|0";
    $specs["type"]            = "enum list|comp|ambu|exte|seances|ssr|psy default|comp";
    $specs["DP"]              = "code cim10";
    $specs["convalescence"]   = "text confidential";
    $specs["rques_sejour"]    = "text confidential";
    $specs["libelle"]         = "str seekable";
    $specs["libelle_sejour"]  = "str seekable";
    $specs["service_id_sejour"] = "ref".(CAppUI::conf("dPplanningOp CSejour service_id_notNull") == 1 ? ' notNull' : '')." class|CService seekable";
    $specs["examen"]          = "text confidential seekable";
    $specs["materiel"]        = "text confidential seekable";
    $specs["duree_hospi"]     = "num notNull min|0 max|36500";
    $specs["rques_operation"] = "text confidential";
    $specs["depassement"]     = "currency min|0 confidential";
    $specs["forfait"]         = "currency min|0 confidential";
    $specs["fournitures"]     = "currency min|0 confidential";
    $specs["pathologie"]      = "str length|3";
    $specs["septique"]        = "bool";
    $specs["codes_ccam"]      = "str";
    $specs["temp_operation"]  = "time";
    $specs["protocole_prescription_chir_id"]   = "ref class|CPrescription";
    $specs["protocole_prescription_anesth_id"] = "ref class|CPrescription";

    $specs["_hour_op"]        = "";
    $specs["_min_op"]         = "";
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->codes_ccam = strtoupper($this->codes_ccam);
    if($this->codes_ccam)
      $this->_codes_ccam = explode("|", $this->codes_ccam);
    else
      $this->_codes_ccam = array();
    $this->_hour_op = intval(substr($this->temp_operation, 0, 2));
    $this->_min_op  = intval(substr($this->temp_operation, 3, 2));
  }

  function updateDBFields() {
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
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
  }

  function loadRefPrescriptionChir() {
    $this->_ref_protocole_prescription_chir = new CPrescription;
    $this->_ref_protocole_prescription_chir->load($this->protocole_prescription_chir_id);
  }

  function loadRefPrescriptionAnesth() {
    $this->_ref_protocole_prescription_anesth = new CPrescription;
    $this->_ref_protocole_prescription_anesth->load($this->protocole_prescription_anesth_id);
  }

  function loadExtCodesCCAM() {
    $this->_ext_codes_ccam = array();
    foreach ($this->_codes_ccam as $code) {
      $this->_ext_codes_ccam[] = CCodeCCAM::get($code, CCodeCCAM::LITE);
    }
  }

  function loadRefsFwd() {
    $this->loadRefChir();
    $this->loadRefPrescriptionChir();
    $this->loadRefPrescriptionAnesth();
    $this->loadExtCodesCCAM();
    $this->_view = "Protocole du Dr {$this->_ref_chir->_view}";
    if($this->libelle) {
      $this->_view .= " - $this->libelle";
    } else {
      foreach($this->_ext_codes_ccam as $key => $ccam) {
        $this->_view .= " - $ccam->code";
      }
    }
  }

  function getPerm($permType) {
    if(!$this->_ref_chir) {
      $this->loadRefChir();
    }
    return $this->_ref_chir->getPerm($permType);
  }
}

?>