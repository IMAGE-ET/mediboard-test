<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Le compte débiteur des règlements
 */
class CDebiteur extends CMbObject {
  // DB Table key
  public $debiteur_id;

  // DB Fields
  public $numero;
  public $nom;
  public $description;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'debiteur';
    $spec->key   = 'debiteur_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["debiteur"] = "CReglement debiteur_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["numero"]      = "num notNull";
    $props["nom"]         = "str notNull maxLength|50";
    $props["description"] = "text";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->numero." - ".$this->nom;
  }
}
