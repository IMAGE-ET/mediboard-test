<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
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

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'etat_dent';
    $spec->key   = 'etat_dent_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["dossier_medical_id"] = "ref notNull class|CDossierMedical";
    $props["dent"]               = "num notNull pos";
    $props["etat"]               = "enum list|bridge|pivot|mobile|appareil|implant|defaut";
    return $props;
  }

  /**
   * @see parent::store()
   */
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
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    return $this->loadRefDossierMedical();
  }

  /**
   * Charge le dossier médical
   *
   * @return CDossierMedical
   */
  function loadRefDossierMedical(){
    return $this->_ref_dossier_medical = $this->loadFwdRef("dossier_medical_id");
  }
}
