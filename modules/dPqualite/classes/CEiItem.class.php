<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Qualite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Element de fiche d'évènement indésirable
 * Class CEiItem
 */
class CEiItem extends CMbObject {
  // DB Table key
  public $ei_item_id;
    
  // DB Fields
  public $ei_categorie_id;
  public $nom;

  // Behaviour Fileds
  public $_checked;

  // Object References
  public $_ref_categorie;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ei_item';
    $spec->key   = 'ei_item_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["ei_categorie_id"] = "ref notNull class|CEiCategorie";
    $specs["nom"]             = "str notNull maxLength|50";
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->_ref_categorie = new CEiCategorie;
    $this->_ref_categorie->load($this->ei_categorie_id);
  }
}
