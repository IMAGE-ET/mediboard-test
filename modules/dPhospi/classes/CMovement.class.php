<?php /* $Id: CIdSante400.class.php 13724 2011-11-09 15:10:29Z lryo $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 13724 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMovement extends CMbObject {
  // DB Table key
  var $movement_id      = null;
  
  // DB fields
  var $sejour_id             = null;
  var $affectation_id        = null;
  var $movement_type         = null;
  var $original_trigger_code = null;
  var $last_update           = null;
  var $cancel                = null;
  
  var $_current              = true;
  
  var $_ref_sejour           = null;
  var $_ref_affectation      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'movement';
    $spec->key   = 'movement_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]             = "ref notNull class|CSejour seekable";
    $props["affectation_id"]        = "ref class|CAffectation seekable";
    $props["movement_type"]         = "enum notNull list|PADM|ADMI|MUTA|SATT|SORT";
    $props["original_trigger_code"] = "str length|3";
    $props["last_update"]           = "dateTime notNull";
    $props["cancel"]                = "bool default|0";
    
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = "$this->movement_type-$this->_id";
  }
  
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", 1);
  }
  
  function loadRefAffectation() {
    return $this->_ref_affectation = $this->loadFwdRef("affectation_id", 1);
  }
  
  function loadMatchingObject($order = null, $group = null, $ljoin = null) {
    $order = "last_update DESC";

    return parent::loadMatchingObject($order, $group, $ljoin);
  }
  
  function loadMatchingList($order = null, $limit = null, $group = null, $ljoin = null) {
    $order = "last_update DESC";

    return parent::loadMatchingList($order, $limit, $group, $ljoin);
  }
  
  function store() {
    // Création idex sur le mouvement (movement_type + original_trigger_code + object_guid + tag (mvt_id))
    
    $this->last_update = mbDateTime();
    
    return parent::store();
  }
    
  function getMovement(CMbObject $object) {
    if ($object instanceof CSejour) {
      $this->sejour_id = $object->_id;
    }
    if ($object instanceof CAffectation) {
      $sejour = $object->_ref_sejour;
      $this->sejour_id      = $sejour->_id;
      $this->affectation_id = $object->_id;
    }
    
    $this->movement_type = $object->getMovementType();
    $this->loadMatchingObject();
  }
}