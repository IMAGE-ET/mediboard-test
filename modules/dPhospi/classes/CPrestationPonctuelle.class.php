<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CPrestationPonctuelle extends CMbObject{
  // DB Table key
  public $prestation_ponctuelle_id;
  
  // DB fields
  public $nom;
  public $group_id;
  public $type_hospi;

  // Form fields
  public $_count_items = 0;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "prestation_ponctuelle";
    $spec->key   = "prestation_ponctuelle_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["nom"]       = "str notNull seekable";
    $props["group_id"]  = "ref notNull class|CGroups";
    $props["type_hospi"] = "enum list|" . implode("|", CSejour::$types) . "|";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["items"] = "CItemPrestation object_id";
   
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  static function loadCurrentList() {
    $prestation = new self();
    $prestation->group_id = CGroups::loadCurrent()->_id;
    return $prestation->loadMatchingList("nom");
  }
  
  static function countCurrentList() {
    $prestation = new self();
    $prestation->group_id = CGroups::loadCurrent()->_id;
    return $prestation->countMatchingList();
  }
}