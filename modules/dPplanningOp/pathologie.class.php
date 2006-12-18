<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Thomas Despoix
*/
 
class CPathologies {
  var $dispo = array (
    "ORT", 
    "ORL", 
    "OPH", 
    "DER", 
    "STO", 
    "GAS", 
    "ARE",
    "RAD",
    "GYN",
    "EST");
    
  var $compat = array();
  
  function CPathologies() {
    $this->addCompat("ORT", "ORT", false, false);

    $this->addCompat("ORL", "ORL", false, false);

    $this->addCompat("OPH", "ORT", null, false);
    $this->addCompat("OPH", "OPH");

    $this->addCompat("DER", "ORT", false, false);
    $this->addCompat("DER", "OPH", false);
    $this->addCompat("DER", "DER", true, true);

    $this->addCompat("STO", "DER", null, false);
    $this->addCompat("STO", "STO");

    $this->addCompat("GAS", "DER", false, false);
    $this->addCompat("GAS", "GAS");

    $this->addCompat("ARE", "ORT", null, false);
    $this->addCompat("ARE", "ORL", null, false);
    $this->addCompat("ARE", "ORT", null, false);
    $this->addCompat("ARE", "OPH");
    $this->addCompat("ARE", "DER", null, false);
    $this->addCompat("ARE", "ARE");

    $this->addCompat("RAD", "ORT", null, false);
    $this->addCompat("RAD", "ORL", null, false);
    $this->addCompat("RAD", "ORT", null, false);
    $this->addCompat("RAD", "OPH");
    $this->addCompat("RAD", "DER", null, false);
    $this->addCompat("RAD", "ARE");
    $this->addCompat("RAD", "RAD");

    $this->addCompat("GYN", "ORT", null, false);
    $this->addCompat("GYN", "ORL", null, false);
    $this->addCompat("GYN", "ORT", null, false);
    $this->addCompat("GYN", "OPH");
    $this->addCompat("GYN", "DER", null, false);
    $this->addCompat("GYN", "RAD");
    $this->addCompat("GYN", "ARE");
    $this->addCompat("GYN", "GYN");
  }

  function addCompat($patho1, $patho2, $septique1 = null, $septique2 = null) {
    assert(in_array($patho1, $this->dispo));
    assert(in_array($patho2, $this->dispo));
    assert($septique1 === null or is_bool($septique1));
    assert($septique2 === null or is_bool($septique2));

    if ($septique1 === null) {
      $this->addCompat($patho1, $patho2, false, $septique2);
      $this->addCompat($patho1, $patho2, true , $septique2);
    }

    if ($septique2 === null) {
      $this->addCompat($patho1, $patho2, $septique1, false);
      $this->addCompat($patho1, $patho2, $septique1, true );
    }
    
    if ($septique1 === null or $septique2 === null) {
			return;
		}

    @$this->compat[$patho1][$septique1][$patho2][$septique2] = true;
  }
  
  
  function isCompat($patho1, $patho2, $septique1, $septique2) {
    assert($septique1 !== null);
    assert($septique2 !== null);

    // bidirectional
    return 
      @$this->compat[$patho1][$septique1][$patho2][$septique2] or
      @$this->compat[$patho2][$septique2][$patho1][$septique1];
  }    
}

?>