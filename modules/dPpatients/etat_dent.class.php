<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Fabien Ménager
*/

/**
 * The CEtatDent Class
 */
class CEtatDent extends CMbObject {
  // DB Table key
  var $etat_dent_id         = null;

  // DB Fields
  var $dossier_medical_id   = null;
  var $dent                 = null;
  var $etat                 = null;

  // Object References
  var $_ref_dossier_medical = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'etat_dent';
    $spec->key   = 'etat_dent_id';
    return $spec;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    
    $specs["dossier_medical_id"] = "ref notNull class|CDossierMedical";
    $specs["dent"]               = "num notNull pos";
    $specs["etat"]               = "enum list|bridge|pivot|mobile|appareil";
    
    return $specs;
  }
  
  function store() {
    $this->updateDBFields();
    
    $etat_dent = new CEtatDent();
    $etat_dent->dent = $this->dent;
    $etat_dent->dossier_medical_id = $this->dossier_medical_id;
    
    if ($etat_dent->loadMatchingObject()) {
      $this->etat_dent_id = $etat_dent->_id;
    }
    return parent::store();
  }

  // Forward references
  function loadRefsFwd() {
    $this->_ref_dossier_medical = new CDossierMedical;
    $this->_ref_dossier_medical->load($this->dossier_medical_id);
  }
}

?>