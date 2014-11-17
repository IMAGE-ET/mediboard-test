<?php 

/**
 * Table type_autorisation_um
 *
 * $Id$
 *  
 * @category PlanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org */


/**
 * Table type_autorisation_um pour le pmsi
 */

class CUniteMedicale extends CMbObject {
  // DB Table key
  public $racine_code;
  public $spec_char;
  public $code_concat;
  public $libelle;
  public $mode_hospitalisation;
  public $sae;

  public $_mode_hospitalisation = array();
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn   = 'sae';
    $spec->table = "type_autorisation_um";
    $spec->key   = "code_concat";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["racine_code"]   = "num notNull maxLength|3";
    $props["spec_char"]     = "str";
    $props["libelle"]       = "text notNull";
    $props["mode_hospitalisation"]   = "str";
    $props["sae"]           = "str";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view      = $this->code_concat." - ".$this->libelle;
    $this->_shortview = $this->code_concat;
    if ($this->mode_hospitalisation) {
      $this->_mode_hospitalisation = explode("|", $this->mode_hospitalisation);
    }
  }

  function loadListUm() {
    return $this->loadList();
  }
}