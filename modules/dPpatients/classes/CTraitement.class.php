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
 * Traitement
 */
class CTraitement extends CMbObject {
  // DB Table key
  public $traitement_id;

  // DB fields
  public $debut;
  public $fin;
  public $traitement;
  public $dossier_medical_id;
  public $annule;

  public $owner_id;
  public $creation_date;

  // Form Fields
  public $_search;

  /** @var CDossierMedical */
  public $_ref_dossier_medical;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'traitement';
    $spec->key   = 'traitement_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["debut"]              = "date progressive";
    $props["fin"]                = "date progressive moreEquals|debut";
    $props["traitement"]         = "text helped seekable";
    $props["dossier_medical_id"] = "ref notNull class|CDossierMedical show|0";
    $props["annule"]             = "bool show|0";
    $props["owner_id"]           = "ref class|CMediusers";
    $props["creation_date"]      = "dateTime";

    $props["_search"] = "str";
    
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->traitement;
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
   * @see parent::store()
   */
  function store() {
    // Save owner and creation date
    if (!$this->_id) {
      $now = CMbDT::dateTime();
      $this->creation_date = $now;

      if (!$this->owner_id) {
        $this->owner_id = CMediusers::get()->_id;
      }
    }

    return parent::store();
  }

  /**
   * Update owner and creation date from user logs
   *
   * @return void
   */
  function updateOwnerAndDates(){
    if (!$this->_id || $this->owner_id && $this->creation_date) {
      return;
    }

    if (empty($this->_ref_logs)) {
      $this->loadLogs();
    }

    $first_log = $this->_ref_first_log;

    $this->owner_id      = $first_log->user_id;
    $this->creation_date = $first_log->date;

    $this->rawStore();
  }

  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();
    $this->loadLogs();
    $this->updateOwnerAndDates();
    $this->loadRefDossierMedical();
  }
}

