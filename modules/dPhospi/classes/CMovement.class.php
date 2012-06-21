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
  var $start_of_movement     = null;
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
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["identifiants"] = "CIdSante400 object_id cascade";
    
    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]             = "ref notNull class|CSejour seekable";
    $props["affectation_id"]        = "ref class|CAffectation seekable cascade";
    $props["movement_type"]         = "enum notNull list|PADM|ADMI|MUTA|SATT|SORT|AABS|RABS|EATT|TATT";
    $props["original_trigger_code"] = "str length|3";
    $props["start_of_movement"]     = "dateTime";
    $props["last_update"]           = "dateTime notNull";
    $props["cancel"]                = "bool default|0";
    
    return $props;
  }
  
  function check() {
    if ($msg = parent::check()) {
      return $msg; 
    }  

    // Check unique affectation_id except absence (leave / return from leave)
    if ($this->movement_type != "AABS" && $this->movement_type != "RABS") {
      $movement = new self;
      $this->completeField("affectation_id");
      $movement->affectation_id = $this->affectation_id;
      $movement->loadMatchingObject();

      if ($this->affectation_id && $movement->_id && $this->_id != $movement->_id) {
        return CAppUI::tr("$this->_class-failed-affectation_id") .
          " : $this->affectation_id";
      }
    }
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
    $order = "start_of_movement DESC, movement_id DESC";

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
      $sejour = $object->loadRefSejour();
      $this->sejour_id      = $sejour->_id;
      $this->affectation_id = $object->_id;
    }

    $this->movement_type = $object->getMovementType();
    $this->loadMatchingObject();
  }
  
  /**
   * Construit le tag d'un mouvement en fonction des variables de configuration
   * @param $group_id Permet de charger l'id externe d'un mouvement pour un établissement donné si non null
   * @return string
   */
  static function getTagMovement($group_id = null) {
    // Pas de tag mouvement
    if (null == $tag_movement = CAppUI::conf("dPhospi CMovement tag")) {
      return;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag_movement);
  }
}