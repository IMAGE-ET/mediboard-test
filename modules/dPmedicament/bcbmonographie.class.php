<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbMonographie extends CBcbObject {

  // Spéciale Monographie
  var $code_cip                 = null;
  var $indications              = null;
  var $contre_indications       = null;
  var $precautions_emploi       = null;
  var $effets_indesirables      = null;
  var $grossesse_allaitement    = null;
  var $interactions             = null;
  var $posologie                = null;
  var $conservation             = null;
  var $incompatibilite          = null;
  var $surdosage                = null;
  var $pharmacodynamie          = null;
  var $pharmacocinetique        = null;
  var $effets_aptitude          = null;
  var $securite_preclinique     = null;
  var $instruction_manipulation = null;
  var $condition_delivrance     = null;
  var $presentation             = null;
  var $date_suppression         = null;
  var $descriptif               = null;
  var $aspect_forme             = null;
  var $emballage_ext            = null;
  
  // Objects references
  var $_ref_produit              = null;
  
  // Constructeur
  function CBcbMonographie(){
    $this->distClass = "BCBMonographie";
    parent::__construct();
  }
 

  function load($cip){
    $this->code_cip                 = $cip;
    $this->indications              = $this->distObj->Text($cip, 0);
    $this->contre_indications       = $this->distObj->Text($cip, 1);     
	  $this->precautions_emploi       = $this->distObj->Text($cip, 2);
	  $this->effets_indesirables      = $this->distObj->Text($cip, 3);
	  $this->grossesse_allaitement    = $this->distObj->Text($cip, 4);
	  $this->interactions             = $this->distObj->Text($cip, 5);
	  $this->posologie                = $this->distObj->Text($cip, 6);
	  $this->conservation             = $this->distObj->Text($cip, 7);
	  $this->incompatibilite          = $this->distObj->Text($cip, 8);
	  $this->surdosage                = $this->distObj->Text($cip, 9);
	  $this->pharmacodynamie          = $this->distObj->Text($cip, 10);
	  $this->pharmacocinetique        = $this->distObj->Text($cip, 11);
	  $this->effets_aptitude          = $this->distObj->Text($cip, 12);
	  $this->securite_preclinique     = $this->distObj->Text($cip, 13);
	  $this->instruction_manipulation = $this->distObj->Text($cip, 14);
	  $this->condition_delivrance     = $this->distObj->Text($cip, 15);
	  $this->presentation             = $this->distObj->Text($cip, 16);
	  $this->date_suppression         = $this->distObj->Text($cip, 18);
	  $this->descriptif               = $this->distObj->Text($cip, 19);
	  $this->aspect_forme             = $this->distObj->Text($cip, 20);
	  $this->emballage_ext            = $this->distObj->Text($cip, 21);
  }
}

?>
