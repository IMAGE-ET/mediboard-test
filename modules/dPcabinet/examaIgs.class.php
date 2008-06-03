<?php 

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:  $
* @author Alexis Granger
*/


class CExamIgs extends CMbObject {
  // DB Table key
  var $examigs_id = null;

  // DB References
  var $consultation_id = null;

  // DB fields
  var $age                 = null;
  var $FC                  = null;
  var $TA                  = null;
  var $temperature         = null;
  var $PAO2_FIO2           = null;
  var $diurese             = null;
  var $uree                = null;
  var $globules_blancs     = null;
  var $kaliemie            = null;
  var $natremie            = null;
  var $HCO3                = null;
  var $billirubine         = null;
  var $glascow             = null;
  var $maladies_chroniques = null;
  var $admission           = null;
  var $scoreIGS            = null;
  
  // Fwd References
  var $_ref_consult = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'examigs';
    $spec->key   = 'examigs_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "consultation_id"     => "notNull ref class|CConsultation",
      "age"                 => "enum list|0|7|12|15|16|18",
      "FC"                  => "enum list|11|2|0|4|7",
      "TA"                  => "enum list|13|5|0|2",
      "temperature"         => "enum list|0|3",
      "PAO2_FIO2"           => "enum list|11|9|6",
      "diurese"             => "enum list|12|4|0",
      "uree"                => "enum list|0|6|10",
      "globules_blancs"     => "enum list|12|0|3",
      "kaliemie"            => "enum list|3a|0|3b",
      "natremie"            => "enum list|5|0|1",
      "HCO3"                => "enum list|6|3|0",
      "billirubine"         => "enum list|0|4|9",
      "glascow"             => "enum list|26|13|7|5|0",
      "maladies_chroniques" => "enum list|9|10|17",
      "admission"           => "enum list|0|6|8",
      "scoreIGS"            => "num"
    );
    return array_merge($specsParent, $specs);
  }

  function loadRefsFwd() {
    $this->_ref_consult = new CConsultation;
    $this->_ref_consult->load($this->consultation_id);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = "Score IGS: $this->scoreIGS";  
  }
  
  function getPerm($permType) {
    if(!$this->_ref_consult) {
      $this->loadRefsFwd();
    }
    return $this->_ref_consult->getPerm($permType);
  }
}

?>