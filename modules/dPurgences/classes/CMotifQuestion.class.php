<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Question du motifs
 */
class CMotifQuestion extends CMbObject {
  public $question_id;

  // DB Fields
  public $motif_id;

  // Form fields
  public $degre;
  public $nom;

  /** @var CMotif */
  public $_ref_motif;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'motif_question';
    $spec->key   = 'question_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["motif_id"] = "ref class|CMotif notNull";
    $props["degre"]    = "num notNull min|1 max|4";
    $props["nom"]      = "text notNull";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["reponses"] = "CMotifReponse question_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  /**
   * Chargement du motif de la question
   *
   * @param bool $cache cache
   *
   * @return CMotif
   */
  function loadRefMotif($cache = true){
    return $this->_ref_motif = $this->loadFwdRef("motif_id", $cache);
  }
}
