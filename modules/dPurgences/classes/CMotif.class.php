<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Motif de l'urgence
 */
class CMotif extends CMbObject {
  public $motif_id;
  
  // DB Fields
  public $chapitre_id;
  
  // Form fields
  public $nom;
  public $code_diag;
  public $degre_min;
  public $degre_max;
  public $definition;
  public $observations;
  public $param_vitaux;
  public $recommande;

  /** @var CChapitreMotif */
  public $_ref_chapitre;

  /** @var CMotifQuestion */
  public $_ref_questions;
  /** @var CMotifQuestion */
  public $_ref_questions_by_degre;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'motif_urgence';
    $spec->key   = 'motif_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["chapitre_id"] = "ref class|CChapitreMotif notNull";
    $props["nom"]         = "text notNull";
    $props["code_diag"]   = "num notNull";
    $props["degre_min"]   = "num notNull min|1 max|4";
    $props["degre_max"]   = "num notNull min|1 max|4";
    $props["definition"]  = "text";
    $props["observations"]= "text";
    $props["param_vitaux"]= "text";
    $props["recommande"]  = "text";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["questions"] = "CMotifQuestion motif_id";
    return $backProps;
  }

  /**
   * updateFormFields
   * 
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  /**
   * Chargement des motifs du chapitre
   * 
   * @param bool $cache cache
   * 
   * @return CChapitreMotif
   */
  function loadRefChapitre($cache = true){
    return $this->_ref_chapitre = $this->loadFwdRef("chapitre_id", $cache);
  }

  /**
   * Chargement des questions du motif
   *
   * @return CMotifQuestion
   */
  function loadRefsQuestions(){
    return $this->_ref_questions = $this->loadBackRefs("questions", 'degre ASC');
  }

  /**
   * Chargement des questions du motif
   *
   * @return CMotifQuestion
   */
  function loadRefsQuestionsByDegre(){
    $this->_ref_questions_by_degre = array();
    foreach ($this->_ref_questions as $question) {
      $this->_ref_questions_by_degre[$question->degre][] = $question;
    }
    return $this->_ref_questions_by_degre;
  }
}
