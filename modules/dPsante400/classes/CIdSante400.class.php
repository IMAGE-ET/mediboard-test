<?php

/**
 * Idex
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

/**
 * Class CIdSante400
 * Idex
 */
class CIdSante400 extends CMbMetaObject {
  public $id_sante400_id;

  // DB fields
  public $id400;
  public $tag;
  public $datetime_create;
  public $last_update;

  // Derivate fields
  public $_last_id;
  
  // Filter fields
  public $_start_date;
  public $_end_date;
  public $_type;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'id_sante400';
    $spec->key   = 'id_sante400_id';
    $spec->loggable = false;
    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @return array
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["id400"]           = "str notNull maxLength|80";
    $specs["tag"]             = "str maxLength|40";
    $specs["datetime_create"] = "dateTime notNull";
    $specs["last_update"]     = "dateTime notNull";

    $specs["_start_date"]    = "dateTime";
    $specs["_end_date"]      = "dateTime";
    return $specs;
  }

  /**
   * Update the plain fields from the form fields
   *
   * @return void
   */
  function updatePlainFields() {
    if ($this->last_update === "") {
      $this->last_update = CMbDT::dateTime();
    }

    parent::updatePlainFields();
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_shortview = "$this->id400";

    $this->_view = "[$this->id400] ($this->tag)";
  }

  /**
   * @see parent::loadMatchingObject
   */
  function loadMatchingObject($order = null, $group = null, $ljoin = null, $index = null) {
    if (!$order) {
      $order = "`last_update` DESC";
    }

     return parent::loadMatchingObject($order, $group, $ljoin, $index);
  }

  /**
   * @see parent::loadMatchingList
   */
  function loadMatchingList($order = null, $limit = null, $group = null, $ljoin = null, $index = null) {
    if (!$order) {
      $order = "`last_update` DESC";
    }
    return parent::loadMatchingList($order, $limit, $group, $ljoin, $index);
  }

  /**
   * @see parent::loadList
   */
  function loadList($where = null, $order = null, $limit = null, $group = null, $ljoin = null, $index = null) {
    if (!$order) {
      $order = "`last_update` DESC";
    }
    return parent::loadList($where, $order, $limit, $group, $ljoin, $index);
  }

  /**
   * @see parent::loadObject
   */
  function loadObject($where = null, $order = null, $group = null, $ljoin = null, $index = null) {
    if (!$order) {
      $order = "`last_update` DESC";
    }
    return parent::loadObject($where, $order, $group, $ljoin, $index);
  }
  
  /**
   * Loads a specific id400 for a given object (and optionnaly tag)
   *
   * @param CMbObject $mbObject Object
   * @param string    $tag      Tag name
   *
   * @return CMbObject Id of the loaded object
   */
  function loadLatestFor($mbObject, $tag = null) {
    $object_class = get_class($mbObject);
    if (!$mbObject instanceof CMbObject) {
      trigger_error("Impossible d'associer un identifiant Santé 400 à un objet de classe '$object_class'");
    }
        
    $this->_id          = null;
    $this->object_class = $object_class;
    $this->object_id    = $mbObject->_id;
    $this->tag          = $tag;
    
    // Don't load if object is undefined
    if ($mbObject->_id) {
      $this->loadMatchingObject();
    }
    
    return $mbObject->_id;
  }

  /**
   * Loads list of idex for a given object and a wildcarded tag
   *
   * @param CMbObject $mbObject Object
   * @param string    $tag      Tag name
   *
   * @return array|CMbObject found ideces
   */
  function loadLikeListFor($mbObject, $tag = null) {
    $object_class = get_class($mbObject);
    if (!$mbObject instanceof CMbObject) {
      trigger_error("Impossible d'associer un identifiant Santé 400 à un objet de classe '$object_class'");
    }
        
    $where["object_id"   ] = "= '$mbObject->_id'";
    $where["object_class"] = "= '$mbObject->_class'";
    $where["tag"] = "LIKE '$tag'";
    
    return $this->loadList($where);
  }
  
  /**
   * Load first idex for a given object and a wildcarded tag
   *
   * @param CMbObject $mbObject Object
   * @param string    $tag      Tag name
   *
   * @return CMbObject found idex
   */
  function loadLikeLatestFor($mbObject, $tag = null) {
    $object_class = get_class($mbObject);
    if (!$mbObject instanceof CMbObject) {
      trigger_error("Impossible d'associer un identifiant Santé 400 à un objet de classe '$object_class'");
    }
        
    $where["object_id"   ] = "= '$mbObject->_id'";
    $where["object_class"] = "= '$mbObject->_class'";
    $where["tag"] = "LIKE '$tag'";

    $this->loadObject($where);
  }
  
  /**
   * Tries to get an already bound object if id400 is not older than delay
   *
   * @param int $delay hours number of cache duration, if null use module config
   *
   * @return CMbObject
   */
  function getCachedObject($delay = null) {
    // Get config cache duration
    if (null === $delay) {
      $delay = CAppUI::conf("dPsante400 cache_hours");
    }
    
    // Look for object
    $this->_id = null;
    $this->loadMatchingObject();
    $this->loadRefsFwd();

    // Check against cache duration
    if (CMbDT::dateTime("+ $delay HOURS", $this->last_update) < CMbDT::dateTime()) {
      $this->_ref_object = new $this->object_class;
    }

    return $this->_ref_object;
  }
  
  /**
   * Tries to get an already bound object if idex
   *
   * @return CMbObject    
   */
  function getMbObject() {
    // Look for object
    $this->_id = null;
    $this->loadMatchingObject();
    $this->loadRefsFwd();

    // Always instanciate
    if (!$this->_ref_object) {
      $this->_ref_object = new $this->object_class;
    }

    return $this->_ref_object;
  }

  /**
   * Binds the idex to an object, and updates the object
   * Will only bind default object properties when it's created
   *
   * @param CMbObject &$mbObject       Object
   * @param CMbObject $mbObjectDefault Default object
   *
   * @throws Exception
   *
   * @return void
   */
  function bindObject(&$mbObject, $mbObjectDefault = null) {
    $object_class = get_class($mbObject);
    if (!$mbObject instanceof CMbObject) {
      trigger_error("Impossible d'associer un identifiant Santé 400 à un objet de classe '$object_class'");
    }

    $this->object_class = $object_class;
    $this->object_id = $mbObject->_id;
    $this->last_update = null; // In case already defined
    $this->loadMatchingObject();
    $this->_ref_object = null; // Prevent optimisation errors
    $this->loadRefs();
    
    // Object has not been found : never created or deleted since last binding
    if (!@$this->_ref_object->_id && $mbObjectDefault) {
      $mbObjectDefault->nullifyEmptyFields();
      $mbObject->extendsWith($mbObjectDefault);
    }
    
    // Create/update bound object
    $mbObject->_id = $this->object_id;
    $mbObject->updatePlainFields();
    $mbObject->repair();
    
    if ($msg = $mbObject->store()) {
      throw new Exception($msg);
    }
    
    $this->object_id = $mbObject->_id;
    $this->last_update = CMbDT::dateTime();

    // Create/update the idSante400    
    if ($msg = $this->store()) {
      throw new Exception($msg);
    }
  }
  
  /**
   * Get match
   *
   * @param string $object_class Object class
   * @param string $tag          Tag name
   * @param string $value        Idex
   * @param string $object_id    Object ID
   *
   * @return CIdSante400 The matching external ID
   */
  static function getMatch($object_class, $tag, $value, $object_id = null) {
    $idex               = new self;
    $idex->object_class = $object_class;
    $idex->tag          = $tag;
    $idex->id400        = $value;
    $idex->object_id    = $object_id;

    $idex->loadMatchingObject();
    
    return $idex;
  }

  /**
   * Get idex value
   *
   * @param string $object_class Object class
   * @param string $tag          Tag name
   * @param string $value        Idex
   * @param string $object_id    Object ID
   *
   * @return string Value the matching external ID
   */
  static function getValue($object_class, $tag, $value, $object_id = null) {
    return self::getMatch($object_class, $tag, $value, $object_id)->id400;
  }

  /**
   * Static shortcut to to idex for a given object
   *
   * @param CMbObject $mbObject Object
   * @param string    $tag      Tag
   * 
   * @return CIdSante400 
   */
  static function getMatchFor(CMbObject $mbObject, $tag = null) {
    $idex = new CIdSante400();
    $idex->loadLatestFor($mbObject, $tag);

    return $idex;
  }

  /**
   * Static shortcut to idex value for a given object
   *
   * @param CMbObject $mbObject Object
   * @param string    $tag      Tag
   * 
   * @return string  
   */
  static function getValueFor(CMbObject $mbObject, $tag = null) {
    return self::getMatchFor($mbObject, $tag)->id400;
  }

  /**
    * Return type if it's special (e.g. IPP/NDA/...)
    *
    * @return string|null
    */
  function getSpecialType() {
    return $this->_type = $this->loadTargetObject()->getSpecialIdex($this);
  }

  /**
   * @see parent::store()
   */
  function store() {
    if (!$this->_id) {
      $this->datetime_create = "now";
    }

    if (!$this->_id && !$this->last_update) {
      $this->last_update = "now";
    }

    return parent::store();
  }

  /**
   * Mass load mechanism for forward references of an object collection
   *
   * @param CMbObject[] $objects Array of objects
   * @param string      $tag     Tag
   *
   * @return self[] Loaded collection, null if unavailable, with ids as keys of guids for meta references
   */
  static function massGetMatchFor($objects, $tag) {
    if (!count($objects)) {
      return array();
    }

    $object = reset($objects);

    $idex                  = new self;
    $where["object_class"] = " = '$object->_class'";
    $where["tag"]          = " = '$tag'";
    $where["object_id"]    = CSQLDataSource::prepareIn(CMbArray::pluck($objects, "_id"));

    $group = "object_id";

    return $idex->loadList($where, null, null, $group);
  }
}