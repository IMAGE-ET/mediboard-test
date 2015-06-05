<?php

/**
 * $Id: $
 *
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Gestion des dossiers de naissance associés aux grossesses.
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
  public $date_time;
  public $rang;
  public $num_naissance;
  public $by_caesarean;

  public $_heure;

  public $fausse_couche;
  public $rques;

  // dates
  public $_day_relative;

  /** @var COperation */
  public $_ref_operation;

  /** @var CGrossesse */
  public $_ref_grossesse;

  /** @var CSejour */
  public $_ref_sejour_enfant;

  /** @var CSejour */
  public $_ref_sejour_maman;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'naissance';
    $spec->key   = 'naissance_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["operation_id"]      = "ref class|COperation";
    $props["grossesse_id"]      = "ref class|CGrossesse";
    $props["sejour_maman_id" ]  = "ref notNull class|CSejour";
    $props["sejour_enfant_id"]  = "ref notNull class|CSejour";
    $props["hors_etab"]         = "bool default|0";
    $props["date_time"]         = "dateTime";
    $props["rang"]              = "num pos";
    $props["num_naissance"]     = "num pos";
    $props["fausse_couche"]     = "enum list|inf_15|sup_15";
    $props["rques"]             = "text helped";
    $props["by_caesarean"]      = "bool notNull default|0";

    $props["_heure"]            = "time notNull";
    return $props;
  }

  /**
   * @see parent::check()
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
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->date_time) {
      $this->_view = $this->getFormattedValue("date_time");
      $this->_heure = CMbDT::time($this->date_time);
    }
    else {
      $this->_view = "Dossier provisoire";
    }
    if ($this->rang) {
      $this->_view .= ", rang " . $this->rang;
    }
  }

  /**
   * @see parent::loadRefsFwd()
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
    $this->_ref_operation = $this->loadFwdRef("operation_id", true);
    if ($this->_ref_operation->date) {
      $this->_day_relative = CMbDT::daysRelative($this->_ref_operation->date, CMbDT::date());
    }
    return $this->_ref_operation;
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
    $this->_ref_sejour_enfant = $this->loadFwdRef("sejour_enfant_id", true);
    return $this->_ref_sejour_enfant;
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
    $group_id = CGroups::loadCurrent()->_id;
    $where = array(
      "fausse_couche IS NULL OR fausse_couche = 'sup_15'",
      "DATE_FORMAT(naissance, '%Y') = " . CMbDT::transform(CMbDT::date(), null, "%Y"),
      "num_naissance IS NOT NULL",
      "sejour.group_id = '$group_id'"
    );
    $ljoin = array(
      "sejour"  => "naissance.sejour_enfant_id = sejour.sejour_id",
      "patients" => "sejour.patient_id = patients.patient_id"
    );

    $naissance = new CNaissance();
    return $naissance->countList($where, null, $ljoin);
  }
}
