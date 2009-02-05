<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

class CExamPossum extends CMbObject {
  // DB Table key
  var $exampossum_id = null;
  
  // DB References
  var $consultation_id = null;
   
  // DB fields
  var $age                  = null;
  var $ouverture_yeux       = null;
  var $rep_verbale          = null;
  var $rep_motrice          = null;
  var $signes_respiratoires = null;
  var $uree                 = null;
  var $freq_cardiaque       = null;
  var $signes_cardiaques    = null;
  var $hb                   = null;
  var $leucocytes           = null;
  var $ecg                  = null;
  var $kaliemie             = null;
  var $natremie             = null;
  var $pression_arterielle  = null;
  var $gravite              = null;
  var $nb_interv            = null;
  var $pertes_sanguines     = null;
  var $contam_peritoneale   = null;
  var $cancer               = null;
  var $circonstances_interv = null;
  
  // Form Fields
  var $_glasgow             = null;
  var $_score_physio        = null;
  var $_score_oper          = null;
  var $_morbidite           = null;
  var $_mortalite           = null;
  var $_score_possum_oper   = null;
  var $_score_possum_physio = null;
  
  // Fwd References
  var $_ref_consult = null;
  
  function CExamPossum() {
    parent::__construct();
    
    static $score_possum_physio = null;
    if (!$score_possum_physio) {
      $score_possum_physio = $this->getScorePhysio();
    }
    $this->_score_possum_physio =& $score_possum_physio;
    
    static $score_possum_oper = null;
    if (!$score_possum_oper) {
      $score_possum_oper = $this->getScoreOper();
    }
    $this->_score_possum_oper =& $score_possum_oper;
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'exampossum';
    $spec->key   = 'exampossum_id';
    return $spec;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      "consultation_id"      => "ref notNull class|CConsultation",
      "age"                  => "enum list|inf60|61|sup71",
      "ouverture_yeux"       => "enum list|spontane|bruit|douleur|jamais",
      "rep_verbale"          => "enum list|oriente|confuse|inapproprie|incomprehensible|aucune",
      "rep_motrice"          => "enum list|obeit|oriente|evitement|decortication|decerebration|rien",
      "signes_respiratoires" => "enum list|aucun|dyspnee_effort|bpco_leger|dyspnee_inval|bpco_modere|dyspnee_repos|fibrose",
      "uree"                 => "enum list|inf7.5|7.6|10.1|sup15.1",
      "freq_cardiaque"       => "enum list|inf39|40|50|81|101|sup121",
      "signes_cardiaques"    => "enum list|aucun|diuretique|antiangineux|oedemes|cardio_modere|turgescence|cardio",
      "hb"                   => "enum list|inf9.9|10|11.5|13|16.1|17.1|sup18.1",
      "leucocytes"           => "enum list|inf3000|3100|4000|10100|sup20100",
      "ecg"                  => "enum list|normal|fa|autre|sup5|anomalie",
      "kaliemie"             => "enum list|inf2.8|2.9|3.2|3.5|5.1|5.4|sup6.0",
      "natremie"             => "enum list|inf125|126|131|sup136",
      "pression_arterielle"  => "enum list|inf89|90|100|110|131|sup171",
      "gravite"              => "enum list|min|moy|maj|maj+",
      "nb_interv"            => "enum list|1|2|sup2",
      "pertes_sanguines"     => "enum list|inf100|101|501|sup1000",
      "contam_peritoneale"   => "enum list|aucune|mineure|purulente|diffusion",
      "cancer"               => "enum list|absense|tumeur|ganglion|metastases",
      "circonstances_interv" => "enum list|reglee|urg|prgm|sansdelai",
      
      // Form Fields
      "_glasgow"             => "",
      "_score_physio"        => "",
      "_score_oper"          => "",
      "_morbidite"           => "",
      "_mortalite"           => "",
      "_score_possum_oper"   => "",
      "_score_possum_physio" => "",
    ));
  }
  
  function getScorePhysio(){
    return array (
      "age"                   => array( "inf60" => 1,
                                         "61"    => 2,
                                         "sup71" => 4),
      "ouverture_yeux"        => array( "spontane" => 4,
                                         "bruit"    => 3,
                                         "douleur"  => 2,
                                         "jamais"   => 1),
      "rep_verbale"           => array( "oriente"          => 5,
                                         "confuse"          => 4,
                                         "inapproprie"      => 3,
                                         "incomprehensible" => 2,
                                         "aucune"           => 1),
      "rep_motrice"           => array( "obeit"         => 6,
                                         "oriente"       => 5,
                                         "evitement"     => 4,
                                         "decortication" => 3,
                                         "decerebration" => 2,
                                         "rien"          => 1),
      "signes_respiratoires" => array( "aucun"         => 1,
                                        "dyspnee_effort"=> 2,
                                        "bpco_leger"    => 2,
                                        "dyspnee_inval" => 4,
                                        "bpco_modere"   => 4,
                                        "dyspnee_repos" => 8,
                                        "fibrose"       => 8),
      "uree"                 => array( "inf7.5"  => 1,
                                        "7.6"     => 2,
                                        "10.1"    => 4,
                                        "sup15.1" => 8),
      "freq_cardiaque"       => array( "inf39"  => 8,
                                        "40"     => 2,
                                        "50"     => 1,
                                        "81"     => 2,
                                        "101"    => 4,
                                        "sup121" => 8),
      "signes_cardiaques"    => array( "aucun"         => 1,
                                        "diuretique"    => 2,
                                        "antiangineux"  => 2,
                                        "oedemes"       => 4,
                                        "cardio_modere" => 4,
                                        "turgescence"   => 8,
                                        "cardio"        => 8),
      "hb"                   => array( "inf9.9"  => 8,
                                        "10"      => 4,
                                        "11.5"    => 2,
                                        "13"      => 1,
                                        "16.1"    => 2,
                                        "17.1"    => 4,
                                        "sup18.1" => 8),
      "leucocytes"           => array( "inf3000"  => 4,
                                        "3100"     => 2,
                                        "4000"     => 1,
                                        "10100"    => 2,
                                        "sup20100" => 4),
      "ecg"                  => array( "normal"   => 1,
                                        "fa"       => 4,
                                        "autre"    => 8,
                                        "sup5"     => 8,
                                        "anomalie" => 8),
      "kaliemie"             => array( "inf2.8" => 8,
                                        "2.9"    => 4,
                                        "3.2"    => 2,
                                        "3.5"    => 1,
                                        "5.1"    => 2,
                                        "5.4"    => 4,
                                        "sup6.0" => 8),
      "natremie"             => array( "inf125" => 8,
                                        "126"    => 4,
                                        "131"    => 2,
                                        "sup136" => 1),
      "pression_arterielle"  => array( "inf89"  => 8,
                                        "90"     => 4,
                                        "100"    => 2,
                                        "110"    => 1,
                                        "131"    => 2,
                                        "sup171" => 4)
    );
  }
  
  function getScoreOper(){
    return array (
      "gravite"              => array( "min"  => 1,
                                        "moy"  => 2,
                                        "maj"  => 4,
                                        "maj+" => 8),
      "nb_interv"            => array( "1"    => 1,
                                        "2"    => 4,
                                        "sup2" => 8),
      "pertes_sanguines"     => array( "inf100"  => 1,
                                        "101"     => 2,
                                        "501"     => 4,
                                        "sup1000" => 8),
      "contam_peritoneale"   => array( "aucune"     => 1,
                                        "mineure"    => 2,
                                        "purulente"  => 4,
                                        "diffusion"  => 8),
      "cancer"               => array( "absense"    => 1,
                                        "tumeur"     => 2,
                                        "ganglion"   => 4,
                                        "metastases" => 8),
      "circonstances_interv" => array( "reglee"    => 1,
                                        "urg"       => 4,
                                        "prgm"      => 4,
                                        "sansdelai" => 8)    
    );
  }
  
  function updateFormFields(){
    // Calcul Glasgow
    $this->_glasgow = 0;
    if($this->ouverture_yeux){
      $this->_glasgow += $this->_score_possum_physio["ouverture_yeux"][$this->ouverture_yeux];
    }
    if($this->rep_verbale){
      $this->_glasgow += $this->_score_possum_physio["rep_verbale"][$this->rep_verbale];
    }
    if($this->rep_motrice){
      $this->_glasgow += $this->_score_possum_physio["rep_motrice"][$this->rep_motrice];
    }
    
    $this->_score_physio = 0;
    foreach($this->_score_possum_physio as $field => $value){
      if($field == "ouverture_yeux" || $field == "rep_verbale"){
        continue;
      }
      if($field == "rep_motrice"){
        if($this->_glasgow >= 1 && $this->_glasgow <= 8){
          $this->_score_physio += 8;
        }elseif($this->_glasgow >= 9 && $this->_glasgow <= 11){
          $this->_score_physio += 4;
        }elseif($this->_glasgow >= 12 && $this->_glasgow <= 14){
          $this->_score_physio += 2;
        }elseif($this->_glasgow == 15){
          $this->_score_physio += 1;
        }
        continue;
      }
      
      if($this->$field){
        $this->_score_physio += $this->_score_possum_physio[$field][$this->$field];
      }
    }
    
    $this->_score_oper = 0;
    foreach($this->_score_possum_oper as $field => $value){
      if($this->$field){
        $this->_score_oper += $this->_score_possum_oper[$field][$this->$field];
      }
    }
    
    //Calcul de la morbidité
    $temp = (0.16 * $this->_score_physio) + (0.19 * $this->_score_oper) - 5.91;
    $this->_morbidite = round(100 / (1 + exp(-$temp)),1);
    
    //Calcul de la Mortalité
    $temp = (0.13 * $this->_score_physio) + (0.16 * $this->_score_oper) - 7.04;
    $this->_mortalite = round(100 / (1 + exp(-$temp)),1);
    
    $this->_view = "Scores POSSUM (morb./mort.) : $this->_morbidite / $this->_mortalite"; 
    

  }
  
  function loadRefsFwd() {
    $this->_ref_consult = new CConsultation;
    $this->_ref_consult->load($this->consultation_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_consult) {
      $this->loadRefsFwd();
    }
    return $this->_ref_consult->getPerm($permType);
  }
}
?>