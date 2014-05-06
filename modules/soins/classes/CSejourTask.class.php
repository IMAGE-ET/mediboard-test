<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSejourTask extends CMbObject {
  public $sejour_task_id;

  // DB Fields
  public $sejour_id;
  public $description;
  public $realise;
  public $resultat;
  public $prescription_line_element_id;
  public $consult_id;
  public $date;
  public $author_id;

  /** @var CSejour */
  public $_ref_sejour;

  /** @var CConsultation */
  public $_ref_consult;

  /** @var CPrescriptionLineElement */
  public $_ref_prescription_line_element;

  /** @var CMediuser */
  public $_ref_author;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour_task';
    $spec->key   = 'sejour_task_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]   = "ref notNull class|CSejour";
    $props["description"] = "text notNull helped";
    $props["realise"]     = "bool default|0";
    $props["resultat"]    = "text helped";
    $props["prescription_line_element_id"] = "ref class|CPrescriptionLineElement";
    $props["consult_id"]  = "ref class|CConsultation";
    $props['date']        = 'dateTime';
    $props['author_id']   = 'ref class|CUser';

    return $props;
  }

  /**
   * @see  parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->description;
  }

  /**
   * Charge le séjour relié à la tâche
   *
   * @return CSejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  /**
   * Charge la consultation reliée à la tâche
   *
   * @return CConsultation
   */
  function loadRefConsult() {
    return $this->_ref_consult = $this->loadFwdRef("consult_id", true);
  }

  /**
   * Charge la ligne d'élément reliée à la tâche
   *
   * @return CPrescriptionLineElement
   */
  function loadRefPrescriptionLineElement(){
    static $active = null;

    if ($active === false) {
      return;
    }

    if ($active === true || ($active = !!CModule::getActive("dPprescription"))) {
      $this->_ref_prescription_line_element = $this->loadFwdRef("prescription_line_element_id");
    }
  }

  /**
   * Charge l'utilisateur qui a créé la tâche
   *
   * @return CUser|null
   */
  function loadRefAuthor() {
    $this->_ref_author = $this->loadFwdRef('author_id', true);
    $this->_ref_author->loadRefMediuser()->loadRefFunction();

    return $this->_ref_author;
  }

  /**
   * Renseigne les champs date et author_id à partir des User logs
   *
   * @return void
   */
  function setDateAndAuthor() {
    if ($this->_id && !$this->date && !$this->author_id) {
      $this->loadFirstLog();
      $this->date = $this->_ref_first_log->date;
      $this->author_id = $this->_ref_first_log->user_id;
      $this->store();
    }
  }

  /**
   * Sort the tasks by date
   *
   * @param CSejourTask[] &$tasks The tasks to sort
   *
   * @return bool
   */
  public static function sortByDate(&$tasks) {
    $res_sort = uasort(
      $tasks,
      function ($a, $b) {
        $at = strtotime($a->date);
        $bt = strtotime($b->date);

        if ($at == $bt) {
          return 0;
        }

        return $at > $bt ? -1 : 1;
      }
    );
  }
}
