<?php
/**
 * $Id: CIdSante400.class.php 13724 2011-11-09 15:10:29Z lryo $
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20044 $
 */

/**
 * Class CMovement
 */
class CMovement extends CMbObject {
  // DB Table key
  public $movement_id;
  
  // DB fields
  public $sejour_id;
  public $affectation_id;
  public $movement_type;
  public $original_trigger_code;
  public $start_of_movement;
  public $last_update;
  public $cancel;

  public $_current = true;

  public $_ref_sejour;
  public $_ref_affectation;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'movement';
    $spec->key   = 'movement_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["identifiants"] = "CIdSante400 object_id cascade";
    
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]             = "ref notNull class|CSejour seekable";
    $props["affectation_id"]        = "ref class|CAffectation seekable nullify";
    $props["movement_type"]         = "enum notNull list|PADM|ADMI|MUTA|SATT|SORT|AABS|RABS|EATT|TATT";
    $props["original_trigger_code"] = "str length|3";
    $props["start_of_movement"]     = "dateTime";
    $props["last_update"]           = "dateTime notNull";
    $props["cancel"]                = "bool default|0";
    
    return $props;
  }

  /**
   * @see parent::check()
   */
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

    return null;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = "$this->movement_type-$this->_id";
  }

  /**
   * Load sejour
   *
   * @return CMbObject|null
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", 1);
  }

  /**
   * Load affectation
   *
   * @return CMbObject|null
   */
  function loadRefAffectation() {
    return $this->_ref_affectation = $this->loadFwdRef("affectation_id", 1);
  }

  /**
   * @see parent::loadMatchingObject()
   */
  function loadMatchingObject($order = null, $group = null, $ljoin = null) {
    $order = "last_update DESC";

    return parent::loadMatchingObject($order, $group, $ljoin);
  }

  /**
   * @see parent::loadMatchingList()
   */
  function loadMatchingList($order = null, $limit = null, $group = null, $ljoin = null) {
    $order = "movement_id DESC, start_of_movement DESC";

    return parent::loadMatchingList($order, $limit, $group, $ljoin);
  }

  /**
   * @see parent::store()
   */
  function store() {
    // Création idex sur le mouvement (movement_type + original_trigger_code + object_guid + tag (mvt_id))
    $this->last_update = CMbDT::dateTime();
    
    return parent::store();
  }

  /**
   * Get movement
   *
   * @param CMbObject $object Object
   *
   * @return void
   */
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
   *
   * @param string $group_id Permet de charger l'id externe d'un mouvement pour un établissement donné si non null
   *
   * @return string
   */
  static function getTagMovement($group_id = null) {
    // Pas de tag mouvement
    if (null == $tag_movement = CAppUI::conf("dPhospi CMovement tag")) {
      return null;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag_movement);
  }
}