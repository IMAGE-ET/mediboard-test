<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

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
  
  function __construct() {
    $this->CMbObject("etat_dent", "etat_dent_id");
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    
    $specs["dossier_medical_id"] = "notNull ref class|CDossierMedical";
    $specs["dent"]               = "notNull num pos";
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