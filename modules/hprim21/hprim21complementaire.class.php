<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPinterop
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

CAppUI::requireModuleClass("hprim21", "hprim21object");

/**
 * The HPRIM 2.1 assurance complémentaire class
 */
class CHprim21Complementaire extends CHprim21Object {
  // DB Table key
	var $hprim21_complementaire_id = null;
  
  // DB references
  var $hprim21_patient_id = null;
	
  // DB Fields
  var $code_organisme  = null;
  var $numero_adherent = null;
  var $debut_droits    = null;
  var $fin_droits      = null;
  var $type_contrat    = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hprim21_complementaire';
    $spec->key   = 'hprim21_complementaire_id';
    return $spec;
  }
  
  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "hprim21_patient_id" => "ref class|CHprim21Patient",
      "code_organisme"     => "str",
      "numero_adherent"    => "str",
      "debut_droits"       => "date",
      "fin_droits"         => "date",
      "type_contrat"       => "str",
    );
    return array_merge($specsParent, $specs);
  }
  
  function bindToLine($line, &$reader, $patient) {
    $this->setEmetteur($reader);
    $this->hprim21_patient_id = $patient->_id;
    
    $elements                 = explode($reader->separateur_champ, $line);
  
    if(count($elements) < 7) {
      $reader->error_log[] = "Champs manquant dans le segment assurance complémentaire";
      return false;
    }
    if(!$elements[2]) {
      $reader->erreo_log[] = "Identifiant externe dans le segment assurance complémentaire";
    }
    
    $this->external_id        = $patient->external_id.$elements[2];
    $this->loadMatchingObject();
    $this->code_organisme     = $elements[2];
    $this->numero_adherent    = $elements[3];
    $this->debut_droits       = $this->getDateFromHprim($elements[4]);
    $this->fin_droits         = $this->getDateFromHprim($elements[5]);
    $this->type_contrat       = $elements[6];
    
    return true;
  }
}
?>