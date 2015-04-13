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
 * Chapitre du motif d'urgence
 */
class CChapitreMotif extends CMbObject {
  public $chapitre_id;

  public $nom;
  
  /** @var CMotif[] */
  public $_ref_motifs;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'motif_chapitre';
    $spec->key   = 'chapitre_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["motif"] = "CMotif chapitre_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["nom"] = "str";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  /**
   * Chargement des motifs du chapitre
   * 
   * @return CMotif[]
   */
  function loadRefsMotifs(){
    $motif = new CMotif();
    $where = array(
      "chapitre_id" => " = '$this->_id'",
    );
    $order = "code_diag";
    return $this->_ref_motifs = $motif->loadList($where, $order);
  }
}
