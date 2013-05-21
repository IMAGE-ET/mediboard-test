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
    $specs = parent::getProps();
    $specs["debut"       ] = "date progressive";
    $specs["fin"         ] = "date progressive moreEquals|debut";
    $specs["traitement"  ] = "text helped seekable";
    $specs["dossier_medical_id"] = "ref notNull class|CDossierMedical show|0";
    $specs["annule"] = "bool show|0";

    $specs["_search"] = "str";
    
    return $specs;
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
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();
    $this->loadLogs();
    $this->loadRefDossierMedical();
  }
}

