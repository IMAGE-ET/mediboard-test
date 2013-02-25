<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPurgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Chapitre du motif d'urgence
 *
 */
class CChapitreMotif extends CMbObject {
  
  // DB Table key
  var $chapitre_id = null;
  
  // DB Fields
  var $nom = null;
  
  // Form fields
  
  // Object References
  var $_ref_motifs = null;
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'motif_chapitre';
    $spec->key   = 'chapitre_id';
    return $spec;
  }
    
  /**
   * getBackProps
   * 
   * @return $backProps
  **/
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["motif"] = "CMotif chapitre_id";
    return $backProps;
  }
   
  /**
   * getProps
   * 
   * @return $props
  **/
  function getProps() {
    $props = parent::getProps();
    $props["nom"]    = "str";
    return $props;
  }
  
  /**
   * updateFormFields
   * 
   * @return void
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  /**
   * Chargement des motifs du chapitre
   * 
   * @return object
  **/
  function loadRefsMotifs(){
    $motif = new CMotif();
    $where = array();
    $where["chapitre_id"] = " = '$this->_id'";
    $this->_ref_motifs = $motif->loadList($where);
  }
}
