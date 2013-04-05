<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Fabien Ménager
*/

/**
 * The CEtatDent Class
 */
class CEtatDent extends CMbObject {
  public $etat_dent_id;

  // DB Fields
  public $dossier_medical_id;
  public $dent;
  public $etat;

  /** @var CDossierMedical */
  public $_ref_dossier_medical;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'etat_dent';
    $spec->key   = 'etat_dent_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["dossier_medical_id"] = "ref notNull class|CDossierMedical";
    $props["dent"]               = "num notNull pos";
    $props["etat"]               = "enum list|bridge|pivot|mobile|appareil|implant|defaut";
    return $props;
  }
  
  function store() {
    if (!$this->_id) {
      $this->updatePlainFields();

      $etat_dent = new CEtatDent();
      $etat_dent->dent = $this->dent;
      $etat_dent->dossier_medical_id = $this->dossier_medical_id;

      if ($etat_dent->loadMatchingObject()) {
        $this->_id = $etat_dent->_id;
      }
    }

    return parent::store();
  }

  /**
   * @return CDossierMedical
   */
  function loadRefsFwd() {
    return $this->_ref_dossier_medical = $this->loadFwdRef("dossier_medical_id");
  }
}
