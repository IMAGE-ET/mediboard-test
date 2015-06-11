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
 * Prestation journalière
 */
class CPrestationJournaliere extends CMbObject {
  // DB Table key
  public $prestation_journaliere_id;
  
  // DB Fields
  public $nom;
  public $group_id;
  public $desire;
  public $type_hospi;

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
    $props = parent::getProps();
    $props["nom"]        = "str notNull";
    $props["group_id"]   = "ref notNull class|CGroups";
    $props["desire"]     = "bool default|0";
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

  /**
   * Charge les prestations journalières de l'établissement
   * pour un éventuel type d'hospitalisation donné
   *
   * @param string $type Type d'hospitalisation
   *
   * @return self[]
   */
  static function loadCurrentList($type = null) {
    $prestation = new self();
    $where = array(
      "group_id" => "= '" . CGroups::loadCurrent()->_id . "'",
    );
    if ($type) {
      $where[] = "type_hospi IS NULL OR type_hospi = '$type'";
    }

    return $prestation->loadList($where, "nom");
  }

  /**
   * Compte les prestations journalières de l'établissement
   * pour un éventuel type d'hospitalisation donné
   * @return int
   */
  static function countCurrentList($type = null) {
    $prestation = new self();
    $where = array(
      "group_id" => "= '" . CGroups::loadCurrent()->_id . "'",
    );
    if ($type) {
      $where[] = "type_hospi IS NULL OR type_hospi = '$type'";
    }

    return $prestation->countList($where, "nom");
  }

  /**
   * Charge les items de la prestation
   *
   * @return CItemPrestation[]
   */
  function loadRefsItems() {
    return $this->_ref_items = $this->loadBackRefs("items", "rank");
  }
}