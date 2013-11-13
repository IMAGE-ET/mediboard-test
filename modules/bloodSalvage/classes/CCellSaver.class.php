<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage bloodSalvage
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * CCellSaver
 */
class CCellSaver extends CMbObject {
  public $cell_saver_id;

  //DB Fields
  public $marque;
  public $modele;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'cell_saver';
    $spec->key   = 'cell_saver_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["marque"] = "str notNull maxLength|50";
    $props["modele"] = "str notNull maxLength|50";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["blood_salvages"] = "CBloodSalvage cell_saver_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->marque $this->modele";
  }
}
