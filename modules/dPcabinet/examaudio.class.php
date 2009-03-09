<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $frequences, $pressions;

$frequences = array(
  "125Hz",
  "250Hz",
  "500Hz",
  "1kHz",
  "2kHz",
  "4kHz",
  "8kHz",
  "16kHz",
);

$nb_pressions = 8;
for ($i = 0; $i < $nb_pressions; $i++) {
	$pressions[] = 100*$i - 400;
}


class CExamAudio extends CMbObject {
  // DB Table key
  var $examaudio_id = null;

  // DB References
  var $consultation_id = null;

  // DB fields
  var $remarques = null;
  
  var $gauche_aerien = null;
  var $gauche_osseux = null;
  var $gauche_conlat = null;
  var $gauche_ipslat = null;
  var $gauche_pasrep = null;
  var $gauche_vocale = null;
  var $gauche_tympan = null;
  
  var $droite_aerien = null;
  var $droite_osseux = null;
  var $droite_conlat = null;
  var $droite_ipslat = null;
  var $droite_pasrep = null;
  var $droite_vocale = null;
  var $droite_tympan = null;
  
  // Form fields
  var $_gauche_aerien = array();
  var $_gauche_osseux = array();
  var $_gauche_conlat = array();
  var $_gauche_ipslat = array();
  var $_gauche_pasrep = array();
  var $_gauche_vocale = array();
  var $_gauche_tympan = array();
  
  var $_droite_aerien = array();
  var $_droite_osseux = array();
  var $_droite_conlat = array();
  var $_droite_ipslat = array();
  var $_droite_pasrep = array();
  var $_droite_vocale = array();
  var $_droite_tympan = array();

  var $_moyenne_gauche_aerien = null;
  var $_moyenne_gauche_osseux = null;
  var $_moyenne_droite_aerien = null;
  var $_moyenne_droite_osseux = null;

  // Fwd References
  var $_ref_consult = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'examaudio';
    $spec->key   = 'examaudio_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "consultation_id" => "ref notNull class|CConsultation",
      "remarques"       => "text helped",
      "gauche_aerien"   => "str maxLength|64",
      "gauche_osseux"   => "str maxLength|64",
      "gauche_conlat"   => "str maxLength|64",
      "gauche_ipslat"   => "str maxLength|64",
      "gauche_pasrep"   => "str maxLength|64",
      "gauche_tympan"   => "str maxLength|64",
      "gauche_vocale"   => "str maxLength|64",
      "droite_aerien"   => "str maxLength|64",
      "droite_osseux"   => "str maxLength|64",
      "droite_conlat"   => "str maxLength|64",
      "droite_ipslat"   => "str maxLength|64",
      "droite_pasrep"   => "str maxLength|64",
      "droite_tympan"   => "str maxLength|64",
      "droite_vocale"   => "str maxLength|64"
    );
    return array_merge($specsParent, $specs);
  }

  function checkAbscisse($vocal_points) {
    $dBs = array();
    foreach($vocal_points as $point) {
      $point = explode("-", $point);
      $dB = $point[0];
      if (array_search($dB, $dBs) !== false) {
        return false;
      }
      
      if ($dB) {
        $dBs[] = $dB;
      }
    }
    
    return true;
  }
  
  function check() {
    $msg = "Deux points ont la même abscisse dans l'audiogramme vocal de l'oreille ";
    if (!$this->checkAbscisse($this->_gauche_vocale)) {
        return $msg . "gauche";
    }

    if (!$this->checkAbscisse($this->_droite_vocale)) {
        return $msg . "droite";
    }

    return parent::check();
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    // Initialisations
    if(!$this->gauche_aerien)
    $this->gauche_aerien = "|||||||";
    if(!$this->gauche_osseux)
    $this->gauche_osseux = "|||||||";
    if(!$this->gauche_conlat)
    $this->gauche_conlat = "|||||||";
    if(!$this->gauche_ipslat)
    $this->gauche_ipslat = "|||||||";
    if(!$this->gauche_pasrep)
    $this->gauche_pasrep = "|||||||";
    if(!$this->gauche_tympan)
    $this->gauche_tympan = "|||||||";
    if(!$this->gauche_vocale)
    $this->gauche_vocale = "|||||||";
    
    if(!$this->droite_aerien)
    $this->droite_aerien = "|||||||";
    if(!$this->droite_osseux)
    $this->droite_osseux = "|||||||";
    if(!$this->droite_conlat)
    $this->droite_conlat = "|||||||";
    if(!$this->droite_ipslat)
    $this->droite_ipslat = "|||||||";
    if(!$this->droite_pasrep)
    $this->droite_pasrep = "|||||||";
    if(!$this->droite_tympan)
    $this->droite_tympan = "|||||||";
    if(!$this->droite_vocale)
    $this->droite_vocale = "|||||||";

    $this->_gauche_aerien = explode("|", $this->gauche_aerien);
    $this->_gauche_osseux = explode("|", $this->gauche_osseux);
    $this->_gauche_conlat = explode("|", $this->gauche_conlat);
    $this->_gauche_ipslat = explode("|", $this->gauche_ipslat);
    $this->_gauche_pasrep = explode("|", $this->gauche_pasrep);
    $this->_gauche_vocale = explode("|", $this->gauche_vocale);
    $this->_gauche_tympan = explode("|", $this->gauche_tympan);

    $this->_droite_aerien = explode("|", $this->droite_aerien);
    $this->_droite_osseux = explode("|", $this->droite_osseux);
    $this->_droite_conlat = explode("|", $this->droite_conlat);
    $this->_droite_ipslat = explode("|", $this->droite_ipslat);
    $this->_droite_pasrep = explode("|", $this->droite_pasrep);
    $this->_droite_vocale = explode("|", $this->droite_vocale);
    $this->_droite_tympan = explode("|", $this->droite_tympan);

    $this->_moyenne_gauche_aerien = ($this->_gauche_aerien[2] + $this->_gauche_aerien[3] + $this->_gauche_aerien[4] + $this->_gauche_aerien[5]) / 4;
    $this->_moyenne_gauche_osseux = ($this->_gauche_osseux[2] + $this->_gauche_osseux[3] + $this->_gauche_osseux[4] + $this->_gauche_osseux[5]) / 4;
    $this->_moyenne_droite_aerien = ($this->_droite_aerien[2] + $this->_droite_aerien[3] + $this->_droite_aerien[4] + $this->_droite_aerien[5]) / 4;
    $this->_moyenne_droite_osseux = ($this->_droite_osseux[2] + $this->_droite_osseux[3] + $this->_droite_osseux[4] + $this->_droite_osseux[5]) / 4;

    foreach($this->_gauche_vocale as $key => $value) {
      $item =& $this->_gauche_vocale[$key]; 
      $item = $value ? explode("-", $value) : array("", "");
    }

    foreach($this->_droite_vocale as $key => $value) {
      $item =& $this->_droite_vocale[$key]; 
      $item = $value ? explode("-", $value) : array("", "");
    }
  }
   
  function updateDBFields() {
    parent::updateDBFields();
    
    // Tris
    $dBs_gauche = array();
    foreach ($this->_gauche_vocale as $key => $value) {
      $dBs_gauche[] = @$value[0] ? @$value[0] : "end sort";
      $this->_gauche_vocale[$key] = @$value[0] . "-" . @$value[1];
    }
    
    array_multisort($dBs_gauche, SORT_ASC, $this->_gauche_vocale);

    $dBs_droite = array();
    foreach($this->_droite_vocale as $key => $value) {
      $dBs_droite[] = @$value[0] ? @$value[0] : "end sort";
      $this->_droite_vocale[$key] = @$value[0] . "-" . @$value[1];
    }
    array_multisort($dBs_droite, SORT_ASC, $this->_droite_vocale);

    // Implodes
    $this->gauche_aerien = implode("|", $this->_gauche_aerien);
    $this->gauche_osseux = implode("|", $this->_gauche_osseux);
    $this->gauche_conlat = implode("|", $this->_gauche_conlat);
    $this->gauche_ipslat = implode("|", $this->_gauche_ipslat);
    $this->gauche_pasrep = implode("|", $this->_gauche_pasrep);
    $this->gauche_vocale = implode("|", $this->_gauche_vocale);
    $this->gauche_tympan = implode("|", $this->_gauche_tympan);
    
    $this->droite_aerien = implode("|", $this->_droite_aerien);
    $this->droite_osseux = implode("|", $this->_droite_osseux);
    $this->droite_conlat = implode("|", $this->_droite_conlat);
    $this->droite_ipslat = implode("|", $this->_droite_ipslat);
    $this->droite_pasrep = implode("|", $this->_droite_pasrep);
    $this->droite_vocale = implode("|", $this->_droite_vocale);
    $this->droite_tympan = implode("|", $this->_droite_tympan);
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