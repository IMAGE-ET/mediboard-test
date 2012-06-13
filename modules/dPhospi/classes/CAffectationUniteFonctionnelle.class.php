<?php /* $Id: CTransmissionMedicale.class.php 12900 2011-08-22 14:07:55Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 12900 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @abstract Permet de relier des unités fonctionnelles à des objets 
 */

class CAffectationUniteFonctionnelle extends CMbMetaObject {
  // DB Table key
  var $affectation_uf_id = null;	
  
  // DB Fields
  var $uf_id = null;
  
  // References
  var $_ref_uf = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'affectation_uf';
    $spec->key   = 'affectation_uf_id';
    $spec->uniques['unique']= array("object_class", "object_id", "uf_id");
    return $spec;
  }

  function getProps() {
  	$props = parent::getProps();
    $props["uf_id"]        = "ref class|CUniteFonctionnelle notNull";
    $props["object_id"]    = "ref class|CMbObject meta|object_class cascade notNull";
  	$props["object_class"] = "enum list|CService|CChambre|CLit|CMediusers|CFunctions|CSejour|CProtocole show|0 notNull";
    return $props;
  }
  
  function loadRefUniteFonctionnelle(){
    return $this->_ref_uf = $this->loadFwdRef("uf_id", true);
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->loadRefUF();
  	$this->_view = $this->_ref_object->_view . " : " . $this->_ref_uf->_view;
  }
}

?>