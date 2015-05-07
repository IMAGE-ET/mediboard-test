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
 * Pathologie en cours des patients
 */
class CPathologie extends CMbObject {
  // DB Table key
  public $pathologie_id;

  // DB fields
  public $debut;
  public $fin;
  public $pathologie;
  public $annule;
  public $dossier_medical_id;
  public $indication_id;
  public $indication_group_id;

  public $owner_id;
  public $creation_date;

  /** @var CDossierMedical */
  public $_ref_dossier_medical;

  public $_ref_indication;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = 'pathologie';
    $spec->key   = 'pathologie_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["debut"]               = "date progressive";
    $props["fin"]                 = "date progressive moreEquals|debut";
    $props["pathologie"]          = "text helped seekable";
    $props["dossier_medical_id"]  = "ref notNull class|CDossierMedical show|0";
    $props["indication_id"]       = "num show|0";
    $props["indication_group_id"] = "num show|0";
    $props["annule"]              = "bool show|0";
    $props["owner_id"]            = "ref notNull class|CMediusers";
    $props["creation_date"]       = "dateTime notNull";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->pathologie;
  }

  /**
   * Charge le dossier médical
   *
   * @return CDossierMedical
   */
  function loadRefDossierMedical() {
    return $this->_ref_dossier_medical = $this->loadFwdRef("dossier_medical_id");
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->loadRefDossierMedical();
  }

  /**
   * @see parent::store()
   */
  function store() {
    // Save owner and creation date
    if (!$this->_id) {
      if (!$this->creation_date) {
        $this->creation_date = CMbDT::dateTime();
      }

      if (!$this->owner_id) {
        $this->owner_id = CMediusers::get()->_id;
      }
    }

    return parent::store();
  }

  function loadRefIndication() {
    $medicament_indication = new CMedicamentIndication();
    return $this->_ref_indication = $medicament_indication->getIndication($this->indication_id, $this->indication_group_id);
  }
}

