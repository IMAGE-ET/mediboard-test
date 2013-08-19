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

/**
 * Prestation journaliere
 */
class CPrestationJournaliere extends CMbObject{
  // DB Table key
  public $prestation_journaliere_id;
  
  // DB Fields
  public $nom;
  public $group_id;
  public $desire;
  
  // Form fields
  public $_count_items = 0;
  public $_ref_items   = 0;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "prestation_journaliere";
    $spec->key   = "prestation_journaliere_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]       = "str notNull";
    $specs["group_id"]  = "ref notNull class|CGroups";
    $specs["desire"]    = "bool default|0";
    
    return $specs;
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

  /**
   * Charge les prestations journalières de l'établissement
   *
   * @return self[]
   */
  static function loadCurrentList() {
    $prestation = new self();
    $prestation->group_id = CGroups::loadCurrent()->_id;
    return $prestation->loadMatchingList("nom");
  }

  /**
   * Compte les prestations journalières de l'établissement
   *
   * @return int
   */
  static function countCurrentList() {
    $prestation = new self();
    $prestation->group_id = CGroups::loadCurrent()->_id;
    return $prestation->countMatchingList();
  }
}