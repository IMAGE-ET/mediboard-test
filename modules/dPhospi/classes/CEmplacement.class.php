<?php  
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * L'emplacement d'une chambre sur un plan
 */
class CEmplacement extends CMbObject {
  
  // DB Table key
  var $emplacement_id = null;
  
  // DB Fields
  var $chambre_id   = null;
  var $plan_x       = null; 
  var $plan_y       = null; 
  var $color        = null; 
  var $hauteur      = null;
  var $largeur      = null;
    
  // Object References
  var $_ref_chambre = null;
   
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'emplacement';
    $spec->key   = 'emplacement_id';
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
    $props["chambre_id"]  = "ref notNull class|CChambre";
    $props["plan_x"]      = "num notNull";
    $props["plan_y"]      = "num notNull";
    $props["color"]       = "str default|DDDDDD notNull maxLength|6";    
    $props["hauteur"]     = "num notNull default|1 min|1 max|20";
    $props["largeur"]     = "num notNull default|1 min|1 max|20";
    return $props;
  }
  
  /**
   * updateFormFields
   * 
   * @return void
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefChambre();
    $this->_view = $this->_ref_chambre->nom;
  }
 
  /**
   * loadRefsFwd
   * 
   * @return void
  **/
  function loadRefsFwd(){ 
    parent::loadRefsFwd();
    $this->loadRefChambre();    
  }
 
  /**
   * Chargement de la chambre concernée par l'emplacement
   * 
   * @return $this->_ref_chambre
  **/
  function loadRefChambre(){ 
    $this->_ref_chambre =  $this->loadFwdRef("chambre_id", true);
    return $this->_ref_chambre;
  }
 
  /**
   * loadRefsBack
   * 
   * @return void
  **/
  function loadRefsBack(){ 
    parent::loadRefsBack();
  }
  
  /**
   * loadRefs
   * 
   * @return void
  **/
  function loadRefs(){
    $this->loadRefsBack();
    $this->loadRefsFwd();
  }
}
?>