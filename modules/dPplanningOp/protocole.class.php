<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain Ollivier
*/

class CProtocole extends CMbObject {
  // DB Table key
  var $protocole_id = null;

  // DB References
  var $chir_id = null; // Sejour / Operation

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
    
  // Form fields
  var $_hour_op    = null;
  var $_min_op     = null;
  var $_codes_ccam = array();
  
  // DB References
  var $_ref_chir = null;
  
  // External references
  var $_ext_codes_ccam = null;

  function CProtocole() {
    $this->CMbObject("protocole","protocole_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "chir_id"         => "ref|notNull",
      "type"            => "enum|comp|ambu|exte",
      "DP"              => "code|cim10",
      "convalescence"   => "str|confidential",
      "rques_sejour"    => "str|confidential",
      "libelle"         => "str|confidential",
      "examen"          => "str|confidential",
      "materiel"        => "str|confidential",
      "duree_hospi"     => "notNull|num|min|0",
      "rques_operation" => "str|confidential",
      "depassement"     => "currency|min|0|confidential"
    );
    $this->_props =& $props;

    static $seek = array (
      "chir_id"  => "ref|CMediusers",
      "libelle"  => "like",
      "examen"   => "like",
      "materiel" => "like"
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
  }

  function check() {    
    return parent::check();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->codes_ccam = strtoupper($this->codes_ccam);
    if($this->codes_ccam)
      $this->_codes_ccam = explode("|", $this->codes_ccam);
    else
      $this->_codes_ccam[0] = "XXXXXX";
    $this->_hour_op = intval(substr($this->temp_operation, 0, 2));
    $this->_min_op  = intval(substr($this->temp_operation, 3, 2));
  }
  
  function updateDBFields() {
    if($this->codes_ccam) {
      $this->codes_ccam = strtoupper($this->codes_ccam);
      $codes_ccam = explode("|", $this->codes_ccam);
      $XPosition = true;
      while($XPosition !== false) {
        $XPosition = array_search("XXXXXXX", $codes_ccam);
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

  function store() {
    if ($msg = parent::store())
      return $msg;
    
    return null;
  }
  
  function loadRefChir() {
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
  }
  
  function loadRefCCAM() {
    $this->_ext_codes_ccam = array();
    foreach ($this->_codes_ccam as $code) {
      $ext_code_ccam = new CCodeCCAM($code);
      $ext_code_ccam->LoadLite();
      $this->_ext_codes_ccam[] = $ext_code_ccam;
    }
    $ext_code_ccam =& $this->_ext_codes_ccam[0];
    $code_ccam = @$this->_codes_ccam[0];
  }
  
  function loadRefsFwd() {
    $this->loadRefChir();
    $this->loadRefCCAM();
    $this->_view = "Protocole du Dr. {$this->_ref_chir->_view}";
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