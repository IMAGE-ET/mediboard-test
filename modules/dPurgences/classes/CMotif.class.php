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
 * Motif de l'urgence
 *
 */
class CMotif extends CMbObject {
  
  // DB Table key
  var $motif_id   = null;
  
  // DB Fields
  var $chapitre_id = null;
  
  // Form fields
  var $nom        = null;
  var $code_diag  = null;
  var $degre_min  = null;
  var $degre_max  = null;
  
  // Object References
  var $_ref_chapitre = null;
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'motif_urgence';
    $spec->key   = 'motif_id';
    return $spec;
  }
    
  /**
   * getBackProps
   * 
   * @return $backProps
  **/
  function getBackProps() {
    $backProps = parent::getBackProps();
    return $backProps;
  }
   
  /**
   * getProps
   * 
   * @return $props
  **/
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
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  /**
   * Chargement des motifs du chapitre
   * 
   * @param bool $cache cache
   * 
   * @return object
  **/
  function loadRefChapitre($cache = true){
    $this->_ref_chapitre = $this->loadFwdRef("chapitre_id", $cache);
  }
}
