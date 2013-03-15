<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CNaissance extends CMbObject {
  // DB Table key
  public $naissance_id;

  // DB References
  public $sejour_maman_id;
  public $sejour_enfant_id;
  public $operation_id;
  public $grossesse_id;
  
  // DB Fields
  public $hors_etab;
  public $heure;
  public $rang;
  public $num_naissance;

  public $fausse_couche;
  public $rques;

  // DB References
  public $_ref_operation;
  public $_ref_grossesse;
  public $_ref_sejour_enfant;
  public $_ref_sejour_maman;

  public $_eai_initiateur_group_id;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'naissance';
    $spec->key   = 'naissance_id';
    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["operation_id"]      = "ref class|COperation";
    $props["grossesse_id"]      = "ref class|CGrossesse";
    $props["sejour_maman_id" ]  = "ref notNull class|CSejour";
    $props["sejour_enfant_id"]  = "ref notNull class|CSejour";
    $props["hors_etab"]         = "bool default|0";
    $props["heure"]             = "time";
    $props["rang"]              = "num pos";
    $props["num_naissance"]     = "num pos";
    $props["fausse_couche"]     = "enum list|inf_15|sup_15";
    $props["rques"]             = "text helped";
    return $props;
  }

  /**
   * Check all properties according to specification
   *
   * @return string Store-like message
   */
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    $this->completeField("operation_id", "sejour_maman_id", "grossesse_id");
    
    // Operation has to be part of sejour
    if ($this->operation_id) {
      $operation = $this->loadRefOperation();
      if ($operation->sejour_id != $this->sejour_maman_id) {
        return "failed-operation-notin-sejour";
      }
    }

    // Sejour has to be part of grossesse
    $sejour = $this->loadRefSejourMaman();
    if ($sejour->grossesse_id != $this->grossesse_id) {
      return "failed-sejour-maman-notin-grossesse";
    }

    return null;
  }

  /**
   * Update the form (derived) fields plain fields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->heure) {
      $this->_view = $this->getFormattedValue("heure");
    }
    else {
      $this->_view = "Dossier provisoire";
    }
    if ($this->rang) {
      $this->_view .= ", rang " . $this->rang;
    }
  }

  /**
   * Forward references global loader
   *
   * @deprecated out of control resouce consumption
   * @return void
   */
  function loadRefsFwd() {
    $this->loadRefOperation();
    $this->loadRefGrossesse();
  }

  /**
   * Operation reference loader
   *
   * @return COperation
   */
  function loadRefOperation() {
    return $this->_ref_operation = $this->loadFwdRef("operation_id", true);
  }

  /**
   * Grossesse reference loader
   *
   * @return CGrossesse
   */
  function loadRefGrossesse() {
    return $this->_ref_grossesse = $this->loadFwdRef("grossesse_id", true);
  }

  /**
   * Child's sejour reference loader
   *
   * @return CSejour
   */
  function loadRefSejourEnfant() {
    return $this->_ref_sejour_enfant = $this->loadFwdRef("sejour_enfant_id", true);
  }

  /**
   * Mother's sejour reference loader
   *
   * @return CSejour
   */
  function loadRefSejourMaman() {
    return $this->_ref_sejour_maman = $this->loadFwdRef("sejour_maman_id", true);
  }

  /**
   * Birth's counter
   *
   * @return int
   */
  static function countNaissances() {
    $where = array(
      "fausse_couche IS NULL OR fausse_couche = 'sup_15'"
    );
    $naissance = new CNaissance();
    return $naissance->countList($where);
  }
}
