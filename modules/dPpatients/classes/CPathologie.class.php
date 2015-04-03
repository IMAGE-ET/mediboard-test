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

  /** @var CDossierMedical */
  public $_ref_dossier_medical;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'pathologie';
    $spec->key   = 'pathologie_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["debut"]              = "date progressive";
    $specs["fin"]                = "date progressive moreEquals|debut";
    $specs["pathologie"]         = "text helped seekable";
    $specs["dossier_medical_id"] = "ref notNull class|CDossierMedical show|0";
    $specs["annule"]             = "bool show|0";
    
    return $specs;
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
  function loadView(){
    parent::loadView();
    $this->loadRefDossierMedical();
  }
}

