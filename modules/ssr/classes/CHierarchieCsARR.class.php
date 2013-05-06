<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Hierarchie CsARR
 */
class CHierarchieCsARR extends CCsARRObject {
  public $code;
  public $libelle;

  public $_ref_parent_hierarchies;
  public $_ref_child_hierarchies;
  public $_ref_activites;
  public $_ref_notes_hierarchie;

  static $cached = array();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hierarchie';
    $spec->key   = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]    = "str notNull length|11 seekable";
    $props["libelle"] = "str notNull seekable";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code;
    $this->_shortview = $this->code;
  }

  function loadRefsParentHierarchies() {
    // Codes des hiérarchies intermédiaires
    $parts = explode(".", $this->code);
    array_pop($parts);
    $codes = array();
    foreach ($parts as $_part) {
      $last = $codes[] = count($codes) ? end($codes) . ".$_part" : $_part;
    }

    // Chargement des hiérarchies intermédiaires
    $hierarchie = new self;
    $hierarchies = $hierarchie->loadAll($codes);
    return $this->_ref_parent_hierarchies = $hierarchies;
  }

  function loadRefsChildHierarchies() {
    $where["code"] = "LIKE '$this->code.__'";
    $hierarchie = new self;
    $hierarchies = $hierarchie->loadList($where);
    return $this->_ref_child_hierarchies = $hierarchies;
  }

  function loadRefsActivites() {
    $activite = new CActiviteCsARR;
    $activite->hierarchie = $this->code;
    $activite = $activite->loadMatchingList();
    return $this->_ref_activites = $activite;
  }

  function loadRefsNotesHierarchies() {
    $note = new CNoteHierarchieCsARR;
    $note->hierarchie = $this->code;
    $notes = array();
    foreach ($note->loadMatchingList("ordre") as $_note) {
      $notes[$_note->typenote][$_note->ordre] = $_note;
    }

    return $this->_ref_notes_hierarchies = $notes;
  }

  /**
   * Get an instance from the code
   *
   * @param string $code
   *
   * @return CActiviteCdARR
   **/
  static function get($code) {
    if (!$code) {
      return new self();
    }

    if (!isset(self::$cached[$code])) {
      $hierarchie = new self();
      $hierarchie->load($code);
      self::$cached[$code] = $hierarchie;
    }

    return self::$cached[$code];
  }
}
