<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage GestionCab
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Employé cabinet
 */
class CEmployeCab extends CMbObject {
  // DB Table key
  public $employecab_id;

  // DB References
  public $function_id;

  // DB Fields
  public $nom;
  public $prenom;
  public $function;
  public $adresse;
  public $cp;
  public $ville;

  /** @var CFunctions */
  public $_ref_function;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'employecab';
    $spec->key   = 'employecab_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["params_paie"] = "CParamsPaie employecab_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["function_id"] = "ref notNull class|CFunctions";
    $props["nom"]         = "str notNull seekable|begin";
    $props["prenom"]      = "str notNull seekable|begin";
    $props["function"]    = "str notNull";
    $props["adresse"]     = "text confidential";
    $props["ville"]       = "str";
    $props["cp"]          = "numchar length|5 confidential";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->nom $this->prenom";
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_function) {
      $this->loadRefsFwd();
    }
    return $this->_ref_function->getPerm($permType);
  }
}
