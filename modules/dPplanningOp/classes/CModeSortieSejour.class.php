<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Mode de sortie
 */
class CModeSortieSejour extends CMbObject {
  // DB Table key
  public $mode_sortie_sejour_id;

  // DB Table key
  public $code;
  public $mode;
  public $group_id;
  public $libelle;
  public $actif;
  public $destination;
  public $orientation;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'mode_sortie_sejour';
    $spec->key   = 'mode_sortie_sejour_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $sejour = new CSejour();
    $rpu = new CRPU();

    $props["code"]        = "str notNull";
    $props["mode"]        = $sejour->_props["mode_sortie"]." notNull";
    $props["group_id"]    = "ref notNull class|CGroups";
    $props["libelle"]     = "str";
    $props["actif"]       = "bool default|1";
    $props['destination'] = $sejour->_props['destination'];
    $props['orientation'] = $rpu->_props['orientation'];

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["sejours"] = "CSejour mode_sortie_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view      = $this->libelle ? $this->libelle : $this->code;
    $this->_shortview = $this->code;
  }
}
