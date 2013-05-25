<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Ligne d'activités RHS
 */
class CLigneActivitesRHS extends CMbObject {  
  // DB Table key
  public $ligne_id;

  // DB Fields
  public $rhs_id;
  public $executant_id;
  public $auto;
  public $code_activite_cdarr;
  public $code_activite_csarr;
  public $code_intervenant_cdarr;

  public $qty_mon;
  public $qty_tue;
  public $qty_wed;
  public $qty_thu;
  public $qty_fri;
  public $qty_sat;
  public $qty_sun;

  // Form fields
  public $_qty_total;
  public $_executant;

  // References
  /** @var CRHS */
  public $_ref_rhs;
  /** @var CIntervenantCdARR */
  public $_ref_intervenant_cdarr;
  /** @var CActiviteCdARR */
  public $_ref_activite_cdarr;
  /** @var CActiviteCsARR */
  public $_ref_activite_csarr;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ligne_activites_rhs";
    $spec->key   = "ligne_id";
    $spec->uniques["ligne"] = array(
      "rhs_id",
      "executant_id",
      "code_activite_cdarr"
    );
    $spec->xor["code"] = array("code_activite_cdarr", "code_activite_csarr");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["rhs_id"]                 = "ref notNull class|CRHS";
    $props["executant_id"]           = "ref notNull class|CMediusers";
    $props["auto"]                   = "bool";
    $props["code_activite_cdarr"]    = "str length|4";
    $props["code_activite_csarr"]    = "str length|7";
    $props["code_intervenant_cdarr"] = "str length|2";
    $props["qty_mon"]                = "num length|1 min|0 max|9 default|0";
    $props["qty_tue"]                = "num length|1 min|0 max|9 default|0";
    $props["qty_wed"]                = "num length|1 min|0 max|9 default|0";
    $props["qty_thu"]                = "num length|1 min|0 max|9 default|0";
    $props["qty_fri"]                = "num length|1 min|0 max|9 default|0";
    $props["qty_sat"]                = "num length|1 min|0 max|9 default|0";
    $props["qty_sun"]                = "num length|1 min|0 max|9 default|0";

    // Form fields
    $props["_qty_total"]             = "num min|0 max|99";
    $props["_executant"]             = "str maxLength|50";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_qty_total = 
      $this->qty_mon + 
      $this->qty_tue + 
      $this->qty_wed + 
      $this->qty_thu +
      $this->qty_fri + 
      $this->qty_sat + 
      $this->qty_sun;

    if (!$this->qty_mon) {
      $this->qty_mon = "";
    }

    if (!$this->qty_tue) {
      $this->qty_tue = "";
    }

    if (!$this->qty_wed) {
      $this->qty_wed = "";
    }

    if (!$this->qty_thu) {
      $this->qty_thu = "";
    }

    if (!$this->qty_fri) {
      $this->qty_fri = "";
    }

    if (!$this->qty_sat) {
      $this->qty_sat = "";
    }

    if (!$this->qty_sun) {
      $this->qty_sun = "";
    }
  }

  /**
   * Charge l'activité CdARR associée
   *
   * @return CActiviteCdARR
   */
  function loadRefActiviteCdARR() {
    $activite = CActiviteCdARR::get($this->code_activite_cdarr);
    $this->_view = $activite->_view;
    return $this->_ref_activite_cdarr = $activite;
  }

  /**
   * Charge l'activité CsARR associée
   *
   * @return CActiviteCsARR
   */
  function loadRefActiviteCsARR() {
    $activite = CActiviteCsARR::get($this->code_activite_csarr);
    $this->_view = $activite->_view;
    return $this->_ref_activite_csarr = $activite;
  }

  /**
   * Chargement l'intervenant CdARR associé
   *
   * @return CIntervenantCdARR
   */
  function loadRefIntervenantCdARR() {
    return $this->_ref_intervenant_cdarr = CIntervenantCdARR::get($this->code_intervenant_cdarr);
  }

  /**
   * Load holding RHS
   * 
   * @return CRHS
   */
  function loadRefRHS() {
    return $this->_ref_rhs = $this->loadFwdRef("rhs_id");
  }

  /**
   * Incremente ou décrement le compteur journalier de la ligne
   *
   * @param datetime $datetime Moment
   * @param string   $action   Soit inc soit dec
   *
   * @return void
   */
  function crementDay($datetime, $action) {
    $day = CMbDT::transform($datetime, null, "%u");
    $days = array(
      "1" => "qty_mon",
      "2" => "qty_tue",
      "3" => "qty_wed",
      "4" => "qty_thu",
      "5" => "qty_fri",
      "6" => "qty_sat",
      "7" => "qty_sun",
    );
    $day = $days[$day];
    $crement = $action == "inc" ? 1 : -1;
    $this->$day += $crement;
  }

  /**
   * @see parent::store()
   */
  function store() {
    // RHS already charged
    $this->completeField("rhs_id");
    $rhs = $this->loadRefRHS();
    if ($rhs->facture) {
      return "$this->_class-failed-rhs-facture";
    }

    // Delete if total is 0
    $this->completeField(
      "qty_mon", 
      "qty_tue", 
      "qty_wed",
      "qty_thu", 
      "qty_thu", 
      "qty_fri", 
      "qty_sat", 
      "qty_sun"
    );
    $this->updateFormFields();
    if ($this->_id && $this->_qty_total == 0) {
      return $this->delete();
    }

    return parent::store();
  }
}
