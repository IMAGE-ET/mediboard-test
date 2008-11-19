<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPinterop
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

CAppUI::requireModuleClass("hprim21", "hprim21object");

/**
 * The HPRIM 2.1 medecin class
 */
class CHprim21Medecin extends CHprim21Object {
  // DB Table key
	var $hprim21_medecin_id = null;
  
  // DB references
  var $user_id = null;
	
  // DB Fields
  var $nom       = null;
  var $prenom    = null;
  var $prenom2   = null;
  var $alias     = null;
  var $civilite  = null;
  var $diplome   = null;
  var $type_code = null;
  
  var $_ref_user = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hprim21_medecin';
    $spec->key   = 'hprim21_medecin_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "user_id"     => "ref class|CMediusers",
      "nom"         => "str",
      "prenom"      => "str",
      "prenom2"     => "str",
      "alias"       => "str",
      "civilite"    => "str",
      "diplome"     => "str",
      "type_code"   => "str",
    );
    return array_merge($specsParent, $specs);
  }

  function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["hprim21_sejours"] = "CHprim21Sejour hprim21_medecin_id";
	  return $backRefs;
	}  
  
	function bindToLine($line, &$reader) {
    $this->setEmetteur($reader);
    
    $elements = explode($reader->separateur_champ, $line);
  
    if(count($elements) < 1) {
      $reader->error_log[] = "Champs manquant dans le segment patient (médecin)";
      return false;
    }
    
    $identite = explode($reader->separateur_sous_champ, $elements[13]);
    if(!$identite[0]) {
      return false;
    }
    $this->external_id = $identite[0];
    $this->loadMatchingObject();
    $this->nom         = $identite[1];
    $this->prenom      = $identite[2];
    $this->prenom2     = $identite[3];
    $this->alias       = $identite[4];
    $this->civilite    = $identite[5];
    
    return true;
  }
  
  function updateFormFields() {
    $this->_view = $this->nom;
  }
  
  function loadRefsFwd(){
    // Chargement du séjour correspondant
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
  }
}
?>