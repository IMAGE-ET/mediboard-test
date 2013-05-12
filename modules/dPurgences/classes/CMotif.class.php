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
  
  /** @var CChapitreMotif */
  public $_ref_chapitre;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'motif_urgence';
    $spec->key   = 'motif_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["chapitre_id"] = "ref class|CChapitreMotif notNull";
    $props["nom"]         = "str notNull";
    $props["code_diag"]   = "num notNull";
    $props["degre_min"]   = "num notNull min|1 max|4";
    $props["degre_max"]   = "num notNull min|1 max|4";
    return $props;
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
}
