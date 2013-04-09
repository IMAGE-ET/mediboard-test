<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CTypeEi extends CMbObject {
  public $type_ei_id;

  //DB Fields
  public $name;
  public $concerne;
  public $desc;
  public $type_signalement;
  public $evenements;

  /** @var array */
  public $_ref_evenement;

  /** @var CEiItem[] */
  public $_ref_items;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'type_ei';
    $spec->key   = 'type_ei_id';
    return $spec;
  }

  /**
   * Spécifications. Indique les formats des différents éléments et références de la classe.
   */
  function getProps() {
    $props = parent::getProps();
    $props["name"]     = "str notNull maxLength|30";
    $props["concerne"] = "enum notNull list|pat|vis|pers|med|mat";
    $props["desc"]     = "text";
    $props["type_signalement"] = "enum notNull list|inc|ris";
    $props["evenements"] = "str notNull maxLength|255";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["blood_salvages"] = "CBloodSalvage type_ei_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;

    if ($this->evenements) {
      $this->_ref_evenement = explode("|", $this->evenements);
    } 
  }

  /**
   * @return CEiItem[]
   */
  function loadRefItems() {
    $this->_ref_items = array();

    foreach ($this->_ref_evenement as $evenement) {
      $ext_item = new CEiItem();
      $ext_item->load($evenement);
      $this->_ref_items[] = $ext_item;
    }

    return $this->_ref_items;
  }
}
