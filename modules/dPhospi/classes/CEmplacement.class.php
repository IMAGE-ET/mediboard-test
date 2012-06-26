<?php  /* $Id: CEmplacement.class.php $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision$
* @author SARL OpenXtrem
* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'emplacement';
    $spec->key   = 'emplacement_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    return $backProps;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["chambre_id"]  = "ref notNull class|CChambre";
    $props["plan_x"]      = "num notNull";
    $props["plan_y"]      = "num notNull";
    $props["color"]       = "str default|DDDDDD notNull maxLength|6";    
    $props["hauteur"]     = "num notNull default|1";
    $props["largeur"]     = "num notNull default|1";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefChambre();
    $this->_view = $this->_ref_chambre->nom;
  }
 
  function loadRefsFwd(){ 
    parent::loadRefsFwd();
    $this->loadRefChambre();    
  }
 
  function loadRefChambre(){ 
    $this->_ref_chambre =  $this->loadFwdRef("chambre_id", true);
    return $this->_ref_chambre;
  }
 
  function loadRefsBack(){ 
    parent::loadRefsBack();
  }
  
  function loadRefs(){
    $this->loadRefsBack();
    $this->loadRefsFwd();
  }
}
?>