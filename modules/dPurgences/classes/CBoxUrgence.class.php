<?php  /* $Id: CBoxUrgences.class.php $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision$
* @author SARL OpenXtrem
* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/

class CBoxUrgence extends CMbObject {
  
  // DB Table key
  var $box_urgences_id = null;
  
  // DB Fields
  var $nom          = null; 
  var $description  = null; 
  var $type         = null; 
  var $plan_x       = null; 
  var $plan_y       = null; 
  var $color        = null; 
  var $hauteur      = null; 
  var $largeur      = null; 
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'box_urgences';
    $spec->key   = 'box_urgences_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    return $backProps;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["nom"]         = "str notNull maxLength|30";
    $props["description"] = "str notNull maxLength|50";
    $props["type"]        = "enum list|Suture|Degravillonage|Dechockage|Traumatologie|Radio|Retour_radio|Imagerie|Bio|Echo|Attente|Resultats|Sortie notNull default|Attente";
    $props["plan_x"]      = "num";
    $props["plan_y"]      = "num";
    $props["color"]       = "str default|ABE notNull maxLength|6";    
    $props["hauteur"]     = "num notNull default|1";    
    $props["largeur"]     = "num notNull default|1";    
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
 
  function loadRefsFwd(){ 
    parent::loadRefsFwd();
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