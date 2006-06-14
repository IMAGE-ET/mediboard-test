<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ('mbobject' ) );

require_once( $AppUI->getModuleClass('mediusers') );

class CProtocole extends CMbObject {
  // DB Table key
  var $protocole_id = null;

  // DB References
  var $chir_id = null; // Sejour / Operation

  // DB Fields Sejour
  var $type = null;
  var $DP = null;
  var $convalescence = null;
  var $rques_sejour = null; // Sejour->rques
  var $pathologie = null;
  var $septique = null;
  
  // DB Fields Operation
  var $codes_ccam = null;
  var $libelle = null;
  var $temp_operation = null;
  var $examen = null;
  var $materiel = null;
  var $duree_hospi = null;
  var $rques_operation = null; // Operation->rques
  var $depassement = null;
    
  // Form fields
  var $_hour_op = null;
  var $_min_op = null;
  var $_codes_ccam = array();
  
  // DB References
  var $_ref_chir = null;
  
  // External references
  var $_ext_codes_ccam = null;

  function CProtocole() {
    $this->CMbObject( 'protocole', 'protocole_id' );

    $this->_props["chir_id"] = "ref|notNull";
    
    $this->_props["type"] = "enum|comp|ambu|exte";
    $this->_props["DP"] = "code|cim10";
    $this->_props["convalescence"] = "str|confidential";
    $this->_props["rques_sejour"] = "str|confidential";
    
    $this->_props["libelle"] = "str|confidential";
    $this->_props["examen"] = "str|confidential";
    $this->_props["materiel"] = "str|confidential";
    $this->_props["duree_hospi"] = "num";
    $this->_props["rques_operation"] = "str|confidential";
    $this->_props["depassement"] = "currency|confidential";
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

    if ($this->libelle !== null && $this->libelle != "") {
      $ext_code_ccam->libelleCourt = "<em>[$this->libelle]</em><br />".$ext_code_ccam->libelleCourt;
      $ext_code_ccam ->libelleLong = "<em>[$this->libelle]</em><br />".$ext_code_ccam->libelleLong;
    }
  }
  
  function loadRefsFwd() {
    $this->loadRefChir();
    $this->loadRefCCAM();
    $this->_view = "Protocole du Dr. {$this->_ref_chir->_view}";
    foreach($this->_ext_codes_ccam as $key => $ccam) {
      $this->_view .= " - $ccam->code";
    }
  }
}

?>