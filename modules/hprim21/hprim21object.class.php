<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPinterop
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The HPRIM 2.1 parent class
 */
class CHprim21Object extends CMbObject {
	
  // DB Fields
  var $emetteur_id          = null;
  var $external_id          = null;
  
  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "emetteur_id" => "str notNull",
      "external_id" => "str",
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    $this->_view = $this->emetteur_id." : ".$this->external_id;
  }
  
  function setEmetteur($reader) {
    $this->emetteur_id = $reader->id_emetteur;
  }
  
  function bindToLine($line, &$reader) {
    return "Bind de $this->_class_name non pris en charge";
  }
  
  function getDateFromHprim($date) {
    if(strlen($date) >= 8) {
      $annee = substr($date, 0, 4);
      $mois = substr($date, 4, 2);
      if($mois == "00") {
        $mois = "01";
      }
      $jour = substr($date, 6, 2);
      if($jour == "00") {
        $jour = "01";
      }
      return "$annee-$mois-$jour";
    } else {
      return "";
    }
  }
  
  function getDateTimeFromHprim($date) {
    if(strlen($date) >= 12) {
      $annee = substr($date, 0, 4);
      $mois = substr($date, 4, 2);
      if($mois == "00") {
        $mois = "01";
      }
      $jour = substr($date, 6, 2);
      if($jour == "00") {
        $jour = "01";
      }
      $heure   = substr($date, 8, 2);
      $minutes = substr($date, 10, 2);
      return "$annee-$mois-$jour $heure:$minutes:00";
    } else {
      return "";
    }
  }
}
?>