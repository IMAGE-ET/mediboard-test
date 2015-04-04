<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Mediboard ORM persistance layer
 * - Persistance: storage, navigation, querying, checking, merging, seeking, cache, userlog 
 * - Configuration: permissions, object configs 
 * - Classification: modules
 */
class CStoredObject extends CModelObject {
  static $useObjectCache = true;
  static $objectCount    = 0;
  static $objectCounts   = array();
  static $objectCache    = array();
  static $cachableCounts = array();

  /** @var CCanDo */
  public $_can;

  /**
   * @var bool Read permission for the object
   * @deprecated
   */
  public $_canRead;

  /**
   * @var bool Edit permission for the object
   * @deprecated
   */
  public $_canEdit;

  public $_external; // true if object is has remote ids
  public $_totalSeek;
  public $_totalWithPerms;

  // Object recorded by sender
  public $_eai_sender_guid;
  
  /**
   * References
   */

  /** @var self[][] Back references collections */
  public $_back = array();

  /** @var int[] Back references counts */
  public $_count = array();

  /** @var self[] Forward references */
  public $_fwd = array();

  /** @var  self[] Array representation of the object's evolution */
  public $_history;

  /**
   * User logs utility references
   */

  /** @var CUserLog[] */
  public $_ref_logs;

  /** @var CUserLog */
  public $_ref_first_log;

  /** @var CUserLog */
  public $_ref_last_log;

  /** @var CUserLog Log related to the current store or delete */
  public $_ref_current_log;
  
  /**
   * The object in database
   * @var self
   */
  public $_old;
  
  // Behaviour fields
  public $_merging;
  public $_purge;
  public $_forwardRefMerging;
  public $_mergeDeletion;
  
  /**
   * Get properties specifications as strings
   *
   * @see parent::getProps()
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    // Add primary key as ref on self class when existing
    if ($key = $this->_spec->key) {
      $props[$key] = "ref class|$this->_class show|0";
    }
    
    return $props;
  }

  /**
   * Check whether object is persistant (ie has a specified table)
   *
   * @return bool
   */
  function hasTable() {
    return $this->_spec->table;
  }
  
  /**
   * Check whether object table is installed
   *
   * @return bool Result
   */
  function isInstalled() {
    return $this->_spec->ds->loadTable($this->_spec->table);
  }

  /**
   * Check whether object exists in table
   *
   * @param int $id The object's identifier
   *
   * @return bool
   */
  function idExists($id) {
    $spec = $this->_spec;

    if (!$id || !$spec->table || !$spec->key) {
      return false;
    }

    $query = "SELECT COUNT(*) FROM `{$spec->table}` WHERE `{$spec->key}` = '$id'";
    return $spec->ds->loadResult($query) > 0;
  }
  
  /** 
   * Load an object by its idendifier
   *
   * @param integer $id [optional] The object's identifier
   *
   * @return CMbObject The loaded object if found, false otherwise
   */
  function load($id = null) {
    if ($id) {
      $this->_id = $id;
    }

    $spec = $this->_spec;

    if (!$this->_id || !$spec->table || !$spec->key) {
      return false;
    }

    $sql = "SELECT * FROM `{$spec->table}` WHERE `{$spec->key}` = '$this->_id'";

    $object = $spec->ds->loadObject($sql, $this);
    
    /* Not envisageable in standard load.
     * Way too much resource consuming
     * 
    if (!$object && CModule::getInstalled("dPsante400")) {
      $idex = new CIdSante400;
      $idex->object_class = $this->_class;
      $idex->id400 = $this->_id;
      $idex->tag = "merged";
      if ($idex->loadMatchingObject()) {
        $this->load($idex->object_id);
        return $this;
      }
    }
    */
    
    if (!$object) {
      $this->_id = null;
      return false;
    }

    $this->checkConfidential();
    $this->registerCache();
    $this->updateFormFields();
    
    return $this;
  } 

  /**
   * Build and load an object with a given GUID
   *
   * @param string $guid   Object GUID
   * @param bool   $cached Use cache
   *
   * @return CMbObject Loaded object, null if inconsistent Guid 
   */
  static function loadFromGuid($guid, $cached = false) {
    list($class, $id) = explode('-', $guid);
    if (!$class) {
      return null;
    }

    $object = self::getInstance($class);

    if (!$object) {
      return null;
    }
    
    if ($id && $id !== "none") {
      return $cached ? $object->getCached($id) : $object->load($id);
    }
    
    return $object;
  }
  
  /**
   * Register the object into cache
   *
   * @return void
   */
  public final function registerCache() {
    if (!self::$useObjectCache) {
      return;
    }
    
    self::$objectCount++;
    
    $class = $this->_class;

    // Statistiques sur chargement d'objets
    if (!isset(self::$objectCounts[$class])) {
      self::$objectCounts[$class] = 0;
    }
    self::$objectCounts[$class]++;
    
    // Statistiques sur cache d'objets
    if (isset(self::$objectCache[$class][$this->_id])) {
      if (!isset(self::$cachableCounts[$class])) {
        self::$cachableCounts[$class] = 0;
      }
      self::$cachableCounts[$class]++;
    }
  
    self::$objectCache[$class][$this->_id] =& $this;
  }
  
  /**
   * Clears the internal CMbObject cache
   *
   * @return void
   */
  public function clearCache(){
    self::$objectCount = 0;
    self::$cachableCounts = array();
    self::$objectCache = array();
  }
  
  /**
   * Retrieve an already registered object from cache if available, performs a standard load otherwise
   *
   * @param integer $id The actual object identifier
   *
   * @return CMbObject the retrieved object
   */
  function getCached($id) {
    if (isset(self::$objectCache[$this->_class][$id])) {
      return self::$objectCache[$this->_class][$id];
    }
    
    $this->load($id);
    return $this;
  }
  
  /**
   * Load the object database version
   * 
   * @return self
   */
  function loadOldObject() {
    if (!$this->_old) {
      $this->_old = new $this->_class;
      $this->_old->load($this->_id);
    }
    
    return $this->_old; 
  }

  /**
   * Nullify modified
   *
   * @return integer Number of fields modified
   */
  function nullifyAlteredFields() {
    $count = 0;
    foreach ($this->getPlainFields() as $_field => $_value) {
      if ($this->fieldAltered($_field)) {
        $this->$_field = null;
        $count++;
      }
    }
    
    return $count;
  }
  
  /**
   * Check whether a field has been modified 
   * 
   * @param string $field Field name
   * @param mixed  $value [optional] Check if modified to given value
   * 
   * @return boolean
   */
  function fieldModified($field, $value = null) {
    // Field is not valued or Nothing in base
    if ($this->$field === null || !$this->_id) {
      return false;
    }    
    
    // Load DB version
    $this->loadOldObject();
    if (!$this->_old->_id) {
      return false;
    }
    
    $spec = $this->_specs[$field];

    // Not formally deterministic case for floats
    if ($spec instanceof CFloatSpec) {
      return !CFloatSpec::equals($this->$field, $this->_old->$field, $spec);
    }
    
    // Check against a specific value
    if ($value !== null && $this->$field != $value) {
      return false;
    }
    
    // Has it finally been modified ?
    return $this->$field != $this->_old->$field;
  }
  
  /**
   * Check whether a field has been modified FROM a non falsy value
   * 
   * @param string $field Field name
   * 
   * @return boolean
   */
  function fieldAltered($field) {
    return $this->fieldModified($field) && $this->_old->$field;
  }
  
  /**
   * Check whether a field has been modified TO a non falsy value
   * 
   * @param string $field Field name
   * 
   * @return boolean
   */
  function fieldValued($field) {
    return $this->fieldModified($field) && $this->$field;
  }

  /**
   * Check whether a field has been modified FROM a non falsy value
   *
   * @param string $field Field name
   *
   * @return boolean
   */
  function fieldFirstModified($field) {
    return $this->fieldModified($field) && $this->_old->$field === null;
  }

  /**
   * Check whether a field has been modified FROM a non falsy value
   *
   * @param string $field Field name
   *
   * @return boolean
   */
  function fieldEmptyValued($field) {
    // Field is not valued or Nothing in base
    if (!$this->_id) {
      return false;
    }

    // Load DB version
    $this->loadOldObject();
    if (!$this->_old->_id || $this->_old->$field === null) {
      return false;
    }

    return ($this->$field != $this->_old->$field) && $this->$field === null;
  }

  /**
   * Check whether an object has been modified (that is at least one of its fields
   * 
   * @return boolean
   */
  function objectModified() {    
    foreach ($this->getPlainFields() as $name => $value) {
      if ($this->fieldModified($name)) {
        return true;
      }
    }
    
    return false;
  }
  
  /**
   * Check whether an object has just been created (no older object)
   * 
   * @return boolean
   */
  function objectCreated() {
    // Load DB version
    $this->loadOldObject();
    
    return  $this->_old->_id;
  }
  
  /**
   * Complete fields with base value if missing
   *
   * @param string[] ... Field names or an array of field names
   *
   * @return void
   */
  function completeField() {
    if (!$this->_id) {
      return;
    }
    
    $fields = func_get_args();
    
    if (isset($fields[0]) && is_array($fields[0])) {
      $fields = $fields[0];
    }
    
    foreach ($fields as $field) {
      // Field is valued
      if ($this->$field !== null) {
        continue;
      }
      
      $this->loadOldObject();
      $this->$field = $this->_old->$field;
    }
  }
  
  /**
   * Load active module
   *
   * @param string $name Name of the module
   *
   * @return CModule
   */
  function loadRefModule($name) {
    return $this->_ref_module = CModule::getActive($name);
  }

  /**
   * Get the configuration of object class for a given conf path
   *
   * @param string $path The configuration path
   *
   * @return string
   */
  function conf($path) {
    $mod_name = $this->_ref_module->mod_name;
    return CAppUI::conf("$mod_name $this->_class $path");
  }
  
  /**
   * Permission generic check
   *
   * @param integer $permType Type of permission : PERM_READ|PERM_EDIT|PERM_DENY
   *
   * @return boolean
   */
  function getPerm($permType) {
    return CPermObject::getPermObject($this, $permType);
  }

  /**
   * Load class level can do
   *
   * @return CCanDo
   */
  function canClass() {
    $can = new CCanDo();

    // Defined at class level in permissions object for user
    global $userPermsObjects;
    if (isset($userPermsObjects[$this->_class][0])) {
      $perm = $userPermsObjects[$this->_class][0];
      $can->read = $perm->permission >= PERM_READ;
      $can->edit = $perm->permission >= PERM_EDIT;
      return $can;
    }

    // Otherwise fall back on module definition
    $can_module = CModule::getCanDo(CModelObject::$module_name[$this->_class]);
    $can->read = $can_module->read;
    $can->edit = $can_module->admin || $can_module->edit;
    return $can;
  }

  /**
   * Gets the can-read boolean permission
   *
   * @todo Should not be used, use canDo()->read instead
   * @return bool
   * @deprecated Do not overload, overload getPerm() instead
   */
  function canRead() {
    return $this->_canRead = $this->getPerm(PERM_READ);
  }
  
  /**
   * Gets the can-edit boolean permission
   *
   * @todo Should not be used, use canDo()->edit instead
   * @return bool
   * @deprecated Do not overload, overload getPerm() instead
   */
  function canEdit() {
    return $this->_canEdit = $this->getPerm(PERM_EDIT);
  }
  
  /**
   * Prevents accessing the objects by redirecting to an "access denied page", 
   * if the user doesn't have READ access
   *
   * @return void
   */
  function needsRead() {
    $can = $this->canDo();
    
    if (!$can->read) {
      $can->redirect();
    }
  }
  
  /**
   * Prevents accessing the objects by redirecting to an "access denied page", 
   * if the user doesn't have EDIT access
   *
   * @return void
   */
  function needsEdit() {
    $can = $this->canDo();
    
    if (!$can->edit) {
      $can->redirect();
    }
  }

  /**
   * Gets the can-do object
   *
   * @return CCanDo 
   */
  function canDo() {
    $can = new CCanDo;
    $can->read  = $this->_canRead = $this->getPerm(PERM_READ);
    $can->edit  = $this->_canEdit = $this->getPerm(PERM_EDIT);

    // Do not give too much information on wanted object
    $can->context = $this->_guid;
    return $this->_can = $can;
  }
  
  /**
   * Permission wise load list alternative, with limit simulation when necessary
   * 
   * @param int    $permType One of PERM_READ, PERM_EDIT
   * @param array  $where    Where SQL statement
   * @param array  $order    Order SQL statement
   * @param string $limit    Limit SQL statement
   * @param array  $group    Group by SQL statement
   * @param array  $ljoin    Left join SQL statement collection
   *
   * @return self[]
   */
  function loadListWithPerms($permType = PERM_READ, $where = null, $order = null, $limit = null, $group = null, $ljoin = null) {
    // Filter with permission
    if (!$permType) {
      $this->_totalWithPerms = $this->countList($where, $group, $ljoin);
      return $this->loadList($where, $order, $limit, $group, $ljoin);
    }

    // Load with no limit
    $list = $this->loadList($where, $order, null, $group, $ljoin);
    self::filterByPerm($list, $permType);
    $this->_totalWithPerms = count($list);
    
    // We simulate the MySQL LIMIT
    if ($limit) {
      $list = CRequest::artificialLimit($list, $limit);
    }
    
    return $list;
  }
  
  /**
   * Filters an object collection according to given permission
   * 
   * @param CStoredObject[] &$objects Objects to be filtered
   * @param int             $permType One of PERM_READ, PERM_EDIT
   *
   * @return int Count of filtered objects
   */
  static function filterByPerm(&$objects = array/*<CMbObject>*/(), $permType = PERM_READ) {
    $total = count($objects);
    foreach ($objects as $id => $object) {
      if (!$object->getPerm($permType)) {
        unset($objects[$id]);
      }
    }
    return $total - count($objects);
  }
  
  /**
   * Load all objects for given identifiers
   * 
   * @param array $ids   list of identifiers
   * @param array $order Order SQL statement
   *
   * @return self[] List of objects
   */
  function loadAll($ids, $order = null) {
    $where[$this->_spec->key] = CSQLDataSource::prepareIn($ids);
    return $this->loadList($where, $order);
  }
  
  /**
   * Loads the first object matching defined properties
   * 
   * @param array|string $order Order SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @param array|string $index Force index
   * 
   * @return integer The found object's ID
   */
  function loadMatchingObject($order = null, $group = null, $ljoin = null, $index = null) {
    $request = new CRequest;
    $request->addLJoin($ljoin);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->addForceIndex($index);

    $this->updatePlainFields();
    $fields = $this->getPlainFields();
    //$ds = $this->getDS();
    foreach ($fields as $key => $value) {
      if ($value !== null) {
        //$value = stripslashes($value); // To "undo" the addslashes from magic_quotes_gpc.php
        //$request->addWhereClause($key, $ds->prepare("=%", $value));
        $request->addWhereClause($key, "= '$value'");
      }
    }

    $this->loadObject($request->where, $request->order, $request->group, $request->ljoin, $request->forceindex);
    return $this->_id;
  }

  /**
   * Loads the first object matching defined properties, escaping the values
   *
   * @param array|string $order Order SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   *
   * @return integer The found object's ID
   */
  function loadMatchingObjectEsc($order = null, $group = null, $ljoin = null) {
    $this->escapeValues();
    $ret = $this->loadMatchingObject($order, $group, $ljoin);
    $this->unescapeValues();
    return $ret;
  }
  
  /**
   * Loads the list of objects matching the $this properties
   * 
   * @param array|string $order Order SQL statement
   * @param string       $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @param array|string $index Force index
   * 
   * @return self[] The list of objects
   */
  function loadMatchingList($order = null, $limit = null, $group = null, $ljoin = null, $index = null) {
    $request = new CRequest;
    $request->addLJoin($ljoin);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);
    $request->addForceIndex($index);

    $this->updatePlainFields();
    $fields = $this->getPlainFields();
    foreach ($fields as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    
    return $this->loadList($request->where, $request->order, $request->limit, $request->group, $request->ljoin, $request->forceindex);
  }
  
  /**
   * Loads the list of objects matching the $this properties, escaping the values
   * 
   * @param array|string $order Order SQL statement
   * @param string       $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @param array|string $index Force index
   * 
   * @return self[] The list of objects
   */
  function loadMatchingListEsc($order = null, $limit = null, $group = null, $ljoin = null, $index = null) {
    $this->escapeValues();
    $ret = $this->loadMatchingList($order, $limit, $group, $ljoin, $index);
    $this->unescapeValues();
    return $ret;
  }
  
  /**
   * Size of the list of objects matching the $this properties
   * 
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @param array|string $index Force index
   * 
   * @return integer The count
   */
  function countMatchingList($group = null, $ljoin = null, $index = null) {
    $request = new CRequest;
    $request->addLJoin($ljoin);
    $request->addGroup($group);
    $request->addForceIndex($index);

    $this->updatePlainFields();
    $fields = $this->getPlainFields();
    foreach ($fields as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    return $this->countList($request->where, $request->group, $request->ljoin, $request->forceindex);
  }
  
  /**
   * Size of the list of objects matching the $this properties, escaping the values
   * 
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @param array|string $index Force index
   * 
   * @return integer The count
   */
  function countMatchingListEsc($group = null, $ljoin = null, $index = null) {
    $this->escapeValues();
    $ret = $this->countMatchingList($group, $ljoin, $index);
    $this->unescapeValues();
    return $ret;
  }
  
  /**
   * Loads the first object matching the query
   * 
   * @param array        $where Where SQL statement
   * @param array|string $order Order SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @param array|string $index Force index
   * 
   * @return boolean True if the object was found
   */
  function loadObject($where = null, $order = null, $group = null, $ljoin = null, $index = null) {
    $list = $this->loadList($where, $order, '0,1', $group, $ljoin, $index);

    if (!$list) {
      return false;
    }

    foreach ($list as $object) {
      $fields = $object->getPlainFields();
      foreach ($fields as $key => $value) {
        $this->$key = $value;
      }
      $this->updateFormFields();
      return true;
    }

    return true;

  }

  /**
   * Object list by a request constructor
   *
   * @param array  $where Where SQL statement
   * @param array  $order Order SQL statement
   * @param string $limit Limit SQL statement
   * @param array  $group Group by SQL statement
   * @param array  $ljoin Left join SQL statement collection
   * @param array  $index Force index
   *
   * @return self[] List of found objects, null if module is not installed
   */
  function loadList($where = null, $order = null, $limit = null, $group = null, $ljoin = null, $index = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addLJoin($ljoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);
    $request->addForceIndex($index);

    return $this->loadQueryList($request->makeSelect($this));
  }
  
  /**
   * Object list for a given group
   * 
   * @param array  $where Where SQL statement
   * @param array  $order Order SQL statement
   * @param string $limit Limit SQL statement
   * @param array  $group Group by SQL statement
   * @param array  $ljoin Left join SQL statement collection
   * 
   * @return self[] List of found objects, null if module is not installed
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $group = null, $ljoin = array()) {
    if (property_exists($this, "group_id")) {
      // Filtre sur l'établissement
      $g = CGroups::loadCurrent();
      $where["group_id"] = "= '$g->_id'";
    }
    return $this->loadList($where, $order, $limit, $group, $ljoin);
  }

  /**
   * Object list for given statements
   *
   * @param array        $where Array of where clauses
   * @param array|string $order Order SQL statement
   * @param string       $limit MySQL limit clause
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Array of left join clauses
   *
   * @return integer[] List of found IDs, null if module is not installed
   */
  function loadIds($where = null, $order = null, $limit = null, $group = null, $ljoin = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addLJoin($ljoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $ds = $this->_spec->ds;
    return $ds->loadColumn($request->makeSelectIds($this));
  }
  
  /**
   * Object count for given statements
   * 
   * @param array        $where Array of where clauses
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Array of left join clauses
   * @param string       $index Force the use of specified index
   *
   * @return int The found objects count, null if module is not installed
   */
  function countList($where = null, $group = null, $ljoin = null, $index = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addForceIndex($index);
    $request->addLJoin($ljoin);
    $request->addWhere($where);
    $request->addGroup($group);

    $ds = $this->_spec->ds;
    return $ds->loadResult($request->makeSelectCount($this));
  }
  
  /**
   * Object count of a multiple list by an SQL request constructor using group-by statement
   *
   * @param array        $where  Array of where clauses
   * @param array|string $order  Order statement
   * @param array|string $group  Group by statement
   * @param array        $ljoin  Array of left join clauses
   * @param array        $fields Append fields to the SELECT
   * @param array|string $index  Force index
   *
   * @return self[]
   */
  function countMultipleList($where = null, $order = null, $group = null, $ljoin = null, $fields = array(), $index = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addWhere($where);
    $request->addOrder($order);
    $request->addGroup($group);
    $request->addLJoin($ljoin);
    $request->addForceIndex($index);
    
    $ds = $this->_spec->ds;
    return $ds->loadList($request->makeSelectCount($this, $fields));
  }
  
  /**
   * Object list by a request object
   *
   * @param CRequest $request Request
   *
   * @return self[] List of found objects, null if module is not installed
   */
  function loadListByReq(CRequest $request) {
    if (!$this->_ref_module) {
      return null;
    }
    
    return $this->loadQueryList($request->makeSelect($this));
  }  
  
  /**
   * return an array of objects from an SQL SELECT query
   *
   * @param string $query SQL Query
   *
   * @todo to optimize request, only select object oids in $query
   * @return self[] List of found objects, null if module is not installed
   */
  function loadQueryList($query) {
    global $m, $action;
    $ds = $this->_spec->ds;
    
    // @todo should replace fetchAssoc, instanciation and bind
    // while ($newObject = $ds->fetchObject($res, $this->_class)) {

    $rows = $ds->loadList($query);

    $objects = array();

    foreach ($rows as $_row) {
      /** @var self $newObject */
      $newObject = new $this->_class;
      $newObject->bind($_row, false);
      
      $newObject->checkConfidential();
      $newObject->updateFormFields();
      $newObject->registerCache();
      
      // Some external classes do not have primary keys
      if ($newObject->_id) {
        $objects[$newObject->_id] = $newObject;
      }
      else {
        $objects[] = $newObject;
      }
    }

    if (count($objects) != count($rows)) {
      mbLog($query,  "Missing group by in $m / $action (rows : ".count($rows).", objects : ".count($objects).")");
    }

    return $objects;
  }
  
  /**
   * References global loader
   *
   * @deprecated out of control resouce consumption
   * @return int Object id
   */
  function loadRefs() {
    if ($this->_id) {
      $this->loadRefsBack();
      $this->loadRefsFwd();
    }
    
    return $this->_id;
  }

  /**
   * Back references global loader
   *
   * @deprecated out of control resouce consumption
   * @return int Object id
   */
  function loadRefsBack() {
  }

  /**
   * Forward references global loader
   *
   * @deprecated out of control resouce consumption
   * @return int Object id
   */
  function loadRefsFwd() {
  }
  
  /**
   * Repair all non checking properties when possible
   *
   * @return string[] if the object is ok an array of message for repaired fields
   */
  function repair() {
    $repaired = array();

    foreach ($this->getProperties() as $name => $value) {
      if ($value !== null) {
        if ($msg = $this->checkProperty($name)) {
          $repaired[$name] = $msg;
          $spec = $this->_specs[$name];
          if (!$spec->notNull) {
            $this->$name = "";
          }
        }
      }
    }

    return $repaired;
  }

  /**
   * Check all properties according to specification
   *
   * @return string|null Store-like message, null when no problem
   */
  function check() {
    $debug = CAppUI::conf("debug");
    
    $msg = "";
    
    // Property level checking
    foreach ($this->_props as $name => $prop) {
      if ($name[0] !== '_') {
        if (!property_exists($this, $name)) {
          trigger_error("La spécification cible la propriété '$name' inexistante dans la classe '$this->_class'", E_USER_WARNING);
        }
        else {
          $value = $this->$name;
          if (!$this->_id || $value !== null) {
            $msgProp = $this->checkProperty($name);
            
            $truncated = CMbString::truncate($value);
            $debugInfo = $debug ? "(val:\"$truncated\", prop:\"$prop\")" : "(valeur: \"$truncated\")";
            $fieldName = CAppUI::tr("$this->_class-$name");
            $msg .= $msgProp ? " &bull; <strong title='$name'>$fieldName</strong> : $msgProp $debugInfo <br/>" : null;
          }
        }
      }
    }
    
    if ($this->_merging) {
      return $msg;
    }

    // Class level unique checking
    foreach ($this->_spec->uniques as $unique => $names) {
      /** @var self $other */
      $other = new $this->_class;
      $values = array();
      foreach ($names as $name) {
        $this->completeField($name);
        $other->$name = addslashes($this->$name);
        
        $value = "";
        
        if ($this->_specs[$name] instanceof CRefSpec) {
          $fwd = $this->loadFwdRef($name);
          
          if ($fwd) {
            $value = $fwd->_view;
          }
        }
        else {
          $value = $this->$name;
        }
        
        $values[] = $value;
      }
      
      $other->loadMatchingObject();
  
      if ($other->_id && $this->_id != $other->_id) {
        return CAppUI::tr("$this->_class-failed-$unique") . " : " . implode(", ", $values);
      }
    }
    
    // Class-level xor checking
    foreach ($this->_spec->xor as $xor => $names) {
      $n = 0;
      $fields = array();
      foreach ($names as $name) {
        $this->completeField($name);
        $fields[] = CAppUI::tr("$this->_class-$name");
        if ($this->$name) {
          $n++;
        }
      }
  
      if ($n != 1) {
        return CAppUI::tr("$this->_class-xorFailed-$xor").
          ": ".implode(", ", $fields).")";
      }
    }
    
    return $msg;
  }

  /**
   * Escape values for SQL queries
   *
   * @return void
   */
  function escapeValues() {
    $values = $this->getProperties();
    foreach ($values as $name => $value) {
      if ($value) {
        $this->$name = addslashes($value);
      }
    }
  }

  /**
   * Unescape Value for SQL queries
   *
   * @return void
   */
  function unescapeValues() {
    $values = $this->getProperties();
    foreach ($values as $name => $value) {
      if ($value) {
        $this->$name = stripslashes($value);
      }
    }
  }
  
  /**
   * Prepare the user log before object persistence
   *
   * @return CUserLog|null null if not loggable
   */
  protected function prepareLog() {
    $this->_ref_current_log = null;
    
    // If the object is not loggable
    if (!$this->_spec->loggable || $this->_purge) {
      return null;
    }

    // Find changed fields
    $fields = array();
    foreach ($this->getPlainFields() as $name => $value) {
      if ($this->fieldModified($name)) {
        $fields[] = $name;
      }
    }
    
    $object_id = $this->_id;
    $old = $this->_old;
    
    $type = "store";
    $extra = null;
    
    if ($old->_id == null) {
      $type = "create";
      $fields = array();
    }
    
    if ($this->_merging) {
      $type = "merge";
    }
      
    if ($this->_id == null) {
      $type = "delete";
      $object_id = $old->_id;
      $extra = $old->_view;
      $fields = array();
    }

    if (!count($fields) && $type === "store") {
      $this->_ref_last_log = null;
      return null;
    }
   
    if ($type === "store" || $type === "merge") {
      $old_values = array();
      $count_not_loggable = 0;
      foreach ($fields as $_field) {
        $_spec = $this->_specs[$_field];
        if (
            $_spec instanceof CTextSpec ||
            $_spec instanceof CHtmlSpec ||
            $_spec instanceof CXmlSpec ||
            $_spec instanceof CPhpSpec ||
            $_spec->loggable == "0"
        ) {
          if ($_spec->loggable == "0") {
            $count_not_loggable++;
          }

          continue;
        }
        $old_values[$_field] = utf8_encode($old->$_field);
      }

      if (count($fields) == $count_not_loggable) {
        return null;
      }

      $extra = json_encode($old_values);
    }
    
    $address = get_remote_address();
    
    $log = new CUserLog();
    $log->user_id = CAppUI::$instance->user_id;
    $log->object_id = $object_id;
    $log->object_class = $this->_class;
    $log->type = $type;
    $log->_fields = $fields;
    $log->date = CMbDT::dateTime();

    // Champs potentiellement absents
    if (CModule::getInstalled("system")->mod_version > "1.0.19") {
      $log->ip_address = $address["client"] ? inet_pton($address["client"]) : null;
      $log->extra = $extra;
    }
    
    return $this->_ref_last_log = $log;
  }
  
  /**
   * Prepare the user log before object persistence (store or delete)
   *
   * @return void
   */
  protected function doLog() {
    // Aucun log à produire (non loggable, pas de modifications, etc.)
    if (!$this->_ref_last_log) {
      return;
    }
    
    $this->_ref_current_log = $this->_ref_last_log;
    $this->_ref_last_log->store();
  }

  /**
   * Load user logs for object
   *
   * @return void
   */
  function loadLogs() {
    $this->_ref_logs = $this->loadBackRefs("logs", "user_log_id DESC", 100, null, null, "object_id");

    /** @var CUserLog $_log */
    foreach ($this->_ref_logs as $_log) {
      $_log->loadRefUser();
      $_log->_ref_object = $this;
    }
    
    // the first is at the end because of the date order !
    $this->_ref_first_log = end($this->_ref_logs);
    $this->_ref_last_log  = reset($this->_ref_logs);
  }
  
  /**
   * Load object state along the time, according to user logs
   *
   * @return void
   */
  function loadHistory() {
    $this->_history = array();
    $this->loadLogs();
    $clone = $this->getPlainFields();
    
    foreach ($this->_ref_logs as $_log) {
      $this->_history[$_log->_id] = $clone;
      
      $_log->getOldValues();
      foreach ($_log->_old_values as $_old_field => $_old_value) {
        $clone[$_old_field] = $_old_value;
      }
    }
  }
    
  /**
   * Load logs concerning a given field
   *
   * @param string $fieldName          Field name
   * @param bool   $strict             Be strict about the field name
   * @param int    $limit              Limit the number of results
   * @param bool   $require_extra_data Return only logs with extra data
   *
   * @return self[]
   */
  function loadLogsForField($fieldName = null, $strict = false, $limit = null, $require_extra_data = false){
    $where = array(); 
    $where["object_id"]    = " = '$this->_id'";
    $where["object_class"] = " = '$this->_class'";
    
    if ($require_extra_data) {
      $where[] = "`extra` IS NOT NULL AND `extra` != '[]' AND `extra` != '{}'";
    }
    
    $log = new CUserLog();
    
    if ($strict) {
      $fields = $fieldName;
      
      if (!is_array($fieldName)) {
        $fields = array($fieldName);
      }
      
      $whereOr = array("`type` = 'create'");
      
      foreach ($fields as $_field) {
        $whereOr[] = "
        `fields` = '$_field' OR 
        `fields` LIKE '$_field %' OR 
        `fields` LIKE '% $_field %' OR 
        `fields` LIKE '% $_field'";
      }
      
      $where[] = implode(" OR ", $whereOr);
    }
    else {
      $where["fields"] = " LIKE '%$fieldName%'";
    }

    return $log->loadList($where, "`user_log_id` DESC", $limit, null, null, "object_id");
  }
  
  /**
   * Load last log concerning a given field
   *
   * @param string $fieldName Field name
   * @param bool   $strict    Be strict about the field name
   *
   * @return CUserLog
   */
  function loadLastLogForField($fieldName = null, $strict = false){
    $log = new CUserLog;
    $logs = $this->loadLogsForField($fieldName, $strict, 1);
    $first_log = reset($logs);
    
    if ($first_log) {
      $first_log->loadRefsFwd();
      return $first_log;
    }
    
    return $log;
  }
  
  /**
   * Load first log concerning a given field
   *
   * @param string $fieldName Field name
   * @param bool   $strict    Be strict about the field name
   *
   * @return CUserLog
   */
  function loadFirstLogForField($fieldName = null, $strict = false){
    $log = new CUserLog;
    $logs = $this->loadLogsForField($fieldName, $strict);
    /** @var CUserLog $first_log */
    $first_log = end($logs);

    if ($first_log) {
      $first_log->loadRefsFwd();
      return $first_log;
    }
    
    return $log;
  }
  
  /**
   * Check wether object has a log more recent than given hours
   * 
   * @param int $nb_hours Number of hours
   *
   * @return int
   */
  function hasRecentLog($nb_hours = 1) {
    $recent = CMbDT::dateTime("- $nb_hours HOURS");
    $where["object_id"   ] = "= '$this->_id'";
    $where["object_class"] = "= '$this->_class'";
    $where["date"] = "> '$recent'";
    $log = new CUserLog();
    return $log->countList($where, null, null, "object_id");
  }
  
  /**
   * Returns the object's latest log
   * 
   * @return CUserLog
   */
  function loadLastLog() {
    return $this->_ref_last_log = $this->loadFirstBackRef("logs", "user_log_id DESC", null, null, "object_id");
  }
  
  /**
   * Returns the object's first log
   * 
   * @return CUserLog
   */
  function loadFirstLog() {
    return $this->_ref_first_log = $this->loadFirstBackRef("logs", "user_log_id ASC", null, null, "object_id");
  }

  /**
   * Return the object's (first) creation log
   * Former instances have legacy data with no creation log but later modification log
   * In that case we explicitely don't want it
   *
   * @return CUserLog
   */
  function loadCreationLog() {
    $log = $this->loadFirstLog();
    return $log->type == "create" || ($log->type == "store" && !$log->fields) ? $log : new CUserLog();
  }

  /**
   * Returns a field's value at the specified date
   * 
   * @param string $date  ISO Date
   * @param string $field Field name
   *
   * @return string
   */
  function getValueAtDate($date, $field) {
    return CUserLog::getObjectValueAtDate($this, $date, $field);
  }
  
  /**
   * Inserts a new row if id is zero or updates an existing row in the database table
   * 
   * @return null|string null if successful otherwise returns and error message
   */
  function store() {
    // Properties checking
    $this->updatePlainFields();

    $this->loadOldObject();
    
    if (CAppUI::conf("readonly")) {
      return CAppUI::tr($this->_class) . 
        CAppUI::tr("CMbObject-msg-store-failed") .
        CAppUI::tr("Mode-readonly-msg");
    }
    
    if ($msg = $this->check()) {
      return CAppUI::tr($this->_class) . 
        CAppUI::tr("CMbObject-msg-check-failed") .
        CAppUI::tr($msg);
    }
    
    // Trigger before event
    $this->notify("BeforeStore");

    $spec = $this->_spec;
    $vars = $this->getPlainFields();
    
    // DB query
    if ($this->_old->_id) {
      $ret = $spec->ds->updateObject($spec->table, $vars, $spec->key, $spec->nullifyEmptyStrings);
    }
    else {
      $keyToUpdate = $spec->incremented ? $spec->key : null;
      $ret = $spec->ds->insertObject($spec->table, $this, $vars, $keyToUpdate, $spec->insert_delayed/*, count($spec->uniques) > 0*/);
    }

    if (!$ret) {
      return CAppUI::tr($this->_class) . 
        CAppUI::tr("CMbObject-msg-store-failed") .
        $spec->ds->error();
    }
    
    // Préparation du log, doit être fait AVANT $this->load()
    $this->prepareLog();


    // Load the object to get all properties
    $this->load();
    
    // Enregistrement du log une fois le store terminé
    $this->doLog();

    // Trigger event
    $this->notify("AfterStore");

    $this->_old = null;
    return null;
  }

  /**
   * Store an object, without creating user log, without properties checking.
   *
   * @return bool
   */
  function rawStore() {
    $spec = $this->_spec;
    $vars = $this->getPlainFields();

    if ($this->_id) {
      return $spec->ds->updateObject($spec->table, $vars, $spec->key, $spec->nullifyEmptyStrings);
    }
    else {
      $key = $spec->incremented ? $spec->key : null;
      return $spec->ds->insertObject($spec->table, $this, $vars, $key, $spec->insert_delayed);
    }
  }

  
  /**
   * Merge an array of objects
   *
   * @param self[] $objects An array of CMbObject to merge
   * @param bool   $fast    Tell wether to use SQL (fast) or PHP (slow but checked and logged) algorithm
   *
   * @return string|null
   */
  function merge($objects, $fast = false) {
    $alternative_mode = ($this->_id != null);
    
    // Modes and object count check
    if ($alternative_mode && count($objects) > 1) {
      return "mergeAlternativeTooManyObjects";
    }
    if (!$alternative_mode && count($objects) < 2) {
      return "mergeTooFewObjects";
    }
    
    // Trigger before event
    $this->notify("BeforeMerge");
    
    if (!$this->_id && $msg = $this->store()) {
      $this->notify("MergeFailure");

      return $msg;
    }

    foreach ($objects as $object) {
      $this->_merging[$object->_id] = $object;
    }

    foreach ($objects as &$object) {
      $msg = $fast ? 
        $this->fastTransferBackRefsFrom($object) :
        $this->transferBackRefsFrom($object);

      if ($msg) {
        $this->notify("MergeFailure");

        return $msg;
      }

      $object_id = $object->_id;
      $object->_mergeDeletion = true;
      if ($msg = $object->delete()) {
        return $msg;
      }

      // If external IDs are available, we save old objects' id as external IDs
      if (CModule::getInstalled("dPsante400")) {
        $idex = new CIdSante400;
        $idex->setObject($this);
        $idex->tag         = "merged";
        $idex->id400       = $object_id;
        $idex->last_update = CMbDT::dateTime();
        $idex->store();
      }
    }

    // Trigger after event
    $this->notify("AfterMerge");
    
    return $this->store();
  }
  
  /**
   * Merges an array of objects
   *
   * @param self[] $objects An array of objects to merge
   *
   * @return string Store-like message
   */
  function checkMerge($objects = array()) {
    $object_class = null;
    foreach ($objects as $object) {
      if (!$object instanceof CMbObject) {
        return 'mergeNotCMbObject';
      }
      if (!$object->_id) {
        return 'mergeNoId';
      }
      if (!$object_class) {
        $object_class = $object->_class;
      }
      else if ($object->_class !== $object_class) {
        return 'mergeDifferentType';
      }
    }

    return null;
  }

  /**
   * Count number back reference collection object
   * 
   * @param string $backName    Name the of the back references to count
   * @param array  $where       Additional where clauses
   * @param array  $ljoin       Additionnal ljoin clauses
   * @param bool   $cache       Cache
   * @param string $backNameAlt BackName Alt
   *
   * @return int|null The count, null if collection count is unavailable
   */
  function countBackRefs($backName, $where = array(), $ljoin = array(), $cache = true, $backNameAlt = "") {
    if (!$backSpec = $this->makeBackSpec($backName)) {
      return null;
    }

    // No existing class
    if (!self::classExists($backSpec->class)) {
      return null;
    }

    $backObject = new $backSpec->class;
    $backField = $backSpec->field;
    
    // Cas du module non installé
    if (!$backObject->_ref_module) {
      return null;
    }

    $backName = $backNameAlt ? $backNameAlt : $backName;
    $cache = $cache && (!count($where) || $backNameAlt);

    // Empty object
    if (!$this->_id || !$backObject->_spec->table || !$backObject->_spec->key) {
      return $this->_count[$backName] = 0;
    }

    // Mass count optimization
    if ($cache && isset($this->_count[$backName])) {
      return $this->_count[$backName];
    }
    
    // @todo Refactor using CRequest
    $query = "SELECT COUNT({$backObject->_spec->key}) 
      FROM `{$backObject->_spec->table}`";
    
    if ($ljoin && count($ljoin)) {
      foreach ($ljoin as $table => $condition) {
        $query .= "\nLEFT JOIN `$table` ON $condition ";
      }
    }
    
    $query .= "WHERE `$backField` = '$this->_id'";

    // Additional where clauses
    foreach ($where as $_field => $_clause) {
      $query .= "\nAND `$_field` $_clause";
    }
    
    // Cas des meta objects
    $backSpec =& $backObject->_specs[$backField];
    $backMeta = $backSpec->meta;
    if ($backMeta) {
      $query .= "\nAND `$backMeta` = '$this->_class'";
    }
    
    // Comptage des backrefs
    return $this->_count[$backName] = $this->_spec->ds->loadResult($query); 
  }

  /**
   * Mass load mechanism for back reference collections of an object collection
   * Will maintain back collections AND back counts
   *
   * @param self[] $objects     Array of objects
   * @param string $backName    Name of backward reference
   * @param null   $order       Order clause
   * @param array  $where       Additional where clauses
   * @param array  $ljoin       Additionnal ljoin clauses
   * @param string $backNameAlt BackName Alt
   *
   * @return int|null|self[] Foundobjects, null if collection is unavailable
   */
  static function massLoadBackRefs($objects, $backName, $order = null, $where = array(), $ljoin = array(), $backNameAlt = "") {
    if (!count($objects)) {
      return null;
    }

    $object = reset($objects);
    if (!$backSpec = $object->makeBackSpec($backName)) {
      return null;
    }

    // No existing class
    if (!self::classExists($backSpec->class)) {
      return null;
    }

    /** @var self $backObject */
    $backObject = new $backSpec->class;
    $backField = $backSpec->field;

    // Cas du module non installé
    if (!$backObject->_ref_module) {
      return null;
    }

    // With old versions of mysql, remove '' fields
    $ids = CMbArray::pluck($objects, "_id");
    CMbArray::removeValue("", $ids);

    $backName = $backNameAlt ? $backNameAlt : $backName;

    // Initilize collections and counts
    foreach ($objects as $_object) {
      $_object->_count[$backName] = 0;
      $_object->_back[$backName] = array();
    }

    // No stored objects
    if (!count($ids)) {
      return 0;
    }

    // Meta objects case
    /** @var CRefSpec $backSpec */
    $backSpec = $backObject->_specs[$backField];
    $backMeta = $backSpec->meta;
    if ($backMeta) {
      $where[$backMeta] = "= '$object->_class'";
    }

    // Actual load query
    $ds = $backObject->_spec->ds;
    $where[$backField] = $ds->prepareIn($ids);
    $backObjects = $backObject->loadList($where, $order, null, null, $ljoin, null);

    // Dispatch back objects into objects collections
    foreach ($backObjects as $_backObject) {
      $object = $objects[$_backObject->$backField];
      $object->_count[$backName]++;
      $object->_back[$backName][$_backObject->_id] = $_backObject;
    }

    // Found objects
    return $backObjects;
  }

  /**
   * Mass count mechanism for back reference collections of an object collection
   *
   * @param self[] $objects     Array of objects
   * @param string $backName    Name of backward reference
   * @param array  $where       Additional where clauses
   * @param array  $ljoin       Additionnal ljoin clauses
   * @param string $backNameAlt BackName Alt
   *
   * @return int|null Total count among objects, null if collection count is unavailable
   */
  static function massCountBackRefs($objects, $backName, $where = array(), $ljoin = array(), $backNameAlt = "") {
    if (!count($objects)) {
      return null;
    }

    $object = reset($objects);
    if (!$backSpec = $object->makeBackSpec($backName)) {
      return null;
    }

    // No existing class
    if (!self::classExists($backSpec->class)) {
      return null;
    }

    /** @var self $backObject */
    $backObject = new $backSpec->class;
    $backField = $backSpec->field;
    
    // Cas du module non installé
    if (!$backObject->_ref_module) {
      return null;
    }

    // With old versions of mysql, remove '' fields
    $ids = CMbArray::pluck($objects, "_id");
    CMbArray::removeValue("", $ids);

    $backName = $backNameAlt ? $backNameAlt : $backName;

    if (!count($ids)) {
      foreach ($objects as $_object) {
        $_object->_count[$backName] = 0;
      }
      return 0;
    }

    // @TODO Refactor using CRequest
    $query = "SELECT $backField, COUNT({$backObject->_spec->key}) 
      FROM `{$backObject->_spec->table}`";
    
    if ($ljoin && count($ljoin)) {
      foreach ($ljoin as $table => $condition) {
        $query .= "\nLEFT JOIN `$table` ON $condition ";
      }
    }
    
    $ds = $backObject->_spec->ds;
    $query .= "WHERE `$backField` " . $ds->prepareIn($ids);

    // Additional where clauses
    foreach ($where as $_field => $_clause) {
      $query .= "\nAND `$_field` $_clause";
    }
    
    // Meta objects case
    /** @var CRefSpec $backSpec */
    $backSpec = $backObject->_specs[$backField];
    $backMeta = $backSpec->meta;
    if ($backMeta) {
      $query .= "\nAND `$backMeta` = '$object->_class'";
    }
    
    // Group by object key
    $query .= "\nGROUP BY $backField";
    $counts = $ds->loadHashList($query);

    // Populate object counts
    $total = 0;
    foreach ($objects as $_object) {
      $count = isset($counts[$_object->_id]) ? $counts[$_object->_id] : 0;
      $total += $_object->_count[$backName] = $count;
    }
    
    // Total count
    return $total; 
  }

  /**
   * Load named back reference collection
   *
   * @param string       $backName    Name of the collection
   * @param array|string $order       Order SQL statement
   * @param string       $limit       MySQL limit clause
   * @param array|string $group       Group by SQL statement
   * @param array        $ljoin       Array of left join clauses
   * @param array        $index       Force index
   * @param string       $backNameAlt BackName Alt
   * @param array        $where       Additional where clauses
   *
   * @return self[]|null Total count among objects, null if collection is unavailable
   */
  function loadBackRefs($backName, $order = null, $limit = null, $group = null, $ljoin = null, $index = null, $backNameAlt = "", $where = array()) {
    if (!$backSpec = $this->makeBackSpec($backName)) {
      return null;
    }
    
    // No existing class
    if (!self::classExists($backSpec->class)) {
      return null;
    }

    /** @var self $backObject */
    $backObject = new $backSpec->class;

    // Module unavailable
    if (!$backObject->_ref_module) {
      return null;
    }

    $backName = $backNameAlt ? $backNameAlt : $backName;

    // Empty object
    if (!$this->_id) {
      return $this->_back[$backName] = array();
    }

    // Precounting optimization: no need to query when we already know array is empty
    if (isset($this->_count[$backName]) && $this->_count[$backName] === 0) {
      return $this->_back[$backName] = array();
    }

    // Preloading optimization: no need to query when we have already preloaded
    // Back collection and back count in correspondance is the "signature" on mass preloading mechanism
    // So that we can use the mechanism safely with probably no side effects
    if (
      array_key_exists($backName, $this->_back) &&
      isset($this->_count[$backName]) &&
      count($this->_back[$backName]) == $this->_count[$backName]) {
      return $this->_back[$backName];
    }

    // Back reference where clause
    $backField = $backSpec->field;
    $where[$backField] = "= '$this->_id'";    
    
    // Meta object case
    /** @var CRefSpec $fwdSpec */
    $fwdSpec = $backObject->_specs[$backField];
    $backMeta = $fwdSpec->meta;
    if ($backMeta) {
      $where[$backMeta] = "= '$this->_class'";
    }
    
    return $this->_back[$backName] = $backObject->loadList($where, $order, $limit, $group, $ljoin, $index);
  }

  /**
   * Load named back reference collection IDs
   *
   * @param string       $backName Name of the collection
   * @param array|string $order    Order SQL statement
   * @param string       $limit    MySQL limit clause
   * @param array|string $group    Group by SQL statement
   * @param array        $ljoin    Array of left join clauses
   *
   * @return ref[]|null IDs collection, null if colletion is unavailable
   */
  function loadBackIds($backName, $order = null, $limit = null, $group = null, $ljoin = null) {
    if (!$backSpec = $this->makeBackSpec($backName)) {
      return null;
    }

    // No existing class
    if (!self::classExists($backSpec->class)) {
      return null;
    }

    /** @var self $backObject */
    $backObject = new $backSpec->class;

    // Cas du module non installé
    if (!$backObject->_ref_module) {
      return null;
    }

    $backField = $backSpec->field;
    /** @var CRefSpec $fwdSpec */
    $fwdSpec = $backObject->_specs[$backField];
    $backMeta = $fwdSpec->meta;
    
    // Cas des meta objects
    if ($backMeta) {
      trigger_error("meta case anavailable", E_USER_ERROR);
    }
    
    // Empty object
    if (!$this->_id) {
      return array();
    }

    // Vérification de la possibilité de supprimer chaque backref
    $where[$backField] = " = '$this->_id'";
    
    return $backObject->loadIds($where, $order, $limit, $group, $ljoin);
  }

  /**
   * Load the unique back reference for given collection name
   * Will check for uniqueness
   *
   * @param string       $backName    The collection name
   * @param array|string $order       Order SQL statement
   * @param string       $limit       MySQL limit clause
   * @param array|string $group       Group by SQL statement
   * @param array        $ljoin       Array of left join clauses
   * @param string       $backNameAlt BackName Alt
   *
   * @return CMbObject Unique back reference if exist, concrete type empty object otherwise, null if unavailable
   */
  function loadUniqueBackRef($backName, $order = null, $limit = null, $group = null, $ljoin = null, $backNameAlt = "") {
    if (null === $backRefs = $this->loadBackRefs($backName, $order, $limit, $group, $ljoin, null, $backNameAlt)) {
      return null;
    }

    $count = count($backRefs);
    if ($count > 1) {
      $ids = array_keys($backRefs);
      $msg = CAppUI::tr(
        "'%s' back reference should be unique (actually counting %s: %s) for object '%s' of class '%s'",
        $backName,
        $count,
        implode(", ", $ids),
        $this->_view,
        $this->_class
      );
      trigger_error($msg, E_USER_WARNING);
    }
    
    if (!$count) {
      $backSpec = $this->_backSpecs[$backName];
      return new $backSpec->class;
    }
    
    return reset($backRefs);
  }

  /**
   * Load the first back reference for given collection name
   *
   * @param string       $backName The collection name
   * @param array|string $order    Order SQL statement
   * @param array|string $group    Group by SQL statement
   * @param array        $ljoin    Array of left join clauses
   * @param array        $index    Force index
   *
   * @return CMbObject Unique back reference if exist, concrete type empty object otherwise, null if unavailable
   */
  function loadFirstBackRef($backName, $order = null, $group = null, $ljoin = null, $index = null) {
    if (null === $backRefs = $this->loadBackRefs($backName, $order, "1", $group, $ljoin, $index)) {
      return null;
    }

    if (!count($backRefs)) {
      $backSpec = $this->_backSpecs[$backName];
      return new $backSpec->class;
    }

    return reset($backRefs);
  }

  /**
   * Load the last back reference for given collection name
   *
   * @param string       $backName The collection name
   * @param array|string $order    Order SQL statement
   * @param string       $limit    MySQL limit clause
   * @param array|string $group    Group by SQL statement
   * @param array        $ljoin    Array of left join clauses
   *
   * @return CMbObject Unique back reference if exist, concrete type empty object otherwise, null if unavailable
   */
  function loadLastBackRef($backName, $order = null, $limit = null, $group = null, $ljoin = null) {
    if (null === $backRefs = $this->loadBackRefs($backName, $order, $limit, $group, $ljoin)) {
      return null;
    }

    if (!count($backRefs)) {
      $backSpec = $this->_backSpecs[$backName];
      return new $backSpec->class;
    }

    return end($backRefs);
  }

  /**
   * Load and count all back references collections
   *
   * @param string $limit Limit SQL query option
   *
   * @return void
   */
  function loadAllBackRefs($limit = null) {
    foreach ($this->_backProps as $backName => $backProp) {
      $backrefs = $this->loadBackRefs($backName, null, $limit);

      $this->_count[$backName] = count($backrefs);

      if ($limit) {
        $this->_count[$backName] = null;
        $this->countBackRefs($backName);
      }
    }
  }
  
  /**
   * Transfer all back refs from given object of same class using unchecked, unlogged SQL queries
   *
   * @param CMbObject &$object The object to transfer back objects from
   *
   * @return string store-like error message if failed, null if successful
   */
  function fastTransferBackRefsFrom(CMbObject &$object) {
    if (!$this->_id) {
      return;
    }

    $this->makeAllBackSpecs();
    $ds = $this->getDS();

    foreach ($this->_backSpecs as $backSpec) {
      // No existing class
      if (!self::classExists($backSpec->class)) {
        continue;
      }
      
      $backObject = new $backSpec->class;
      $backField = $backSpec->field;

      // Cas du module non installé
      if (!$backObject->_ref_module) {
        continue;
      }
  
      // Unstored object
      if (!$backObject->_spec->table || !$backObject->_spec->key) {
        continue;
      }
      
      $query = "UPDATE `{$backObject->_spec->table}`
        SET `$backField` = '$this->_id'
        WHERE `$backField` = '$object->_id'";
  
      // Cas des meta objects
      $fwdSpec =& $backObject->_specs[$backField];
      $backMeta = $fwdSpec->meta;      
      if ($backMeta) {
        $query .= "\nAND `$backMeta` = '$object->_class'";
      }

      $ds->exec($query);
    }    
  }
  
  /**
   * Transfer all back refs from given object of same class
   *
   * @param CMbObject &$object The object to transfer back objects from
   *
   * @return string|null store-like error message if failed, null if successful
   */
  function transferBackRefsFrom(CMbObject &$object) {
    if (!$object->_id) {
      trigger_error("transferNoId");
    }

    if ($object->_class !== $this->_class) {
      trigger_error("An object from type '$object->_class' can't be merge with an object from type '$this->_class'", E_USER_ERROR);
    }
    
    $object->loadAllBackRefs();
    foreach ($object->_back as $backName => $backObjects) {
      /** @var self[] $backObjects */
      if (count($backObjects)) {
        $backSpec = $this->_backSpecs[$backName];
        $backObject = new $backSpec->class;
        $backField = $backSpec->field;
        $fwdSpec = $backObject->_specs[$backField];
        $backMeta = $fwdSpec->meta;      
        
        // Change back field and store back objects
        foreach ($backObjects as $backObject) {
          /** @var self $transferer Dummy tranferer object to prevent checks on all values */
          $transferer = new $backObject->_class;
          $transferer->_id = $backObject->_id;
          $transferer->$backField = $this->_id;
          $transferer->_forwardRefMerging = true;
          
          // Cas des meta objects
          if ($backMeta) {
            $transferer->$backMeta = $this->_class;
          }
          
          if ($msg = $transferer->store()) {
            return $msg;
          }
        }
      }
    }

    return null;
  }
  
  /**
   * Load named forward reference
   *
   * @param string $field  Field name
   * @param bool   $cached Use object cache when possible
   *
   * @return CMbObject|null concrete loaded object, null if reference unavailable
   */
  function loadFwdRef($field, $cached = false) {
    // Object scope cache
    if ($cached && isset($this->_fwd[$field]) && $this->_fwd[$field]->_id) {
      return $this->_fwd[$field];
    }

    // Not a ref spec
    $spec = $this->_specs[$field];
    if (!$spec instanceof CRefSpec) {
      return null;
    }
    
    // Undefined class
    $class = $spec->meta ? $this->{$spec->meta} : $spec->class;
    if (!$class) {
      return $this->_fwd[$field] = null;
    }
          
    // Non existing class
    if (!self::classExists($class)) {
      return $this->_fwd[$field] = null;
    }

    /** @var self $fwd */
    $fwd = new $class;
    
    // Inactive module
    if (!$fwd->_ref_module) {
      return $this->_fwd[$field] = null;
    }
    
    // Actual loading or cache fetching
    if ($cached) {
      $fwd = $fwd->getCached($this->$field);
    }
    else {
      $fwd->load($this->$field);
    }
    
    return $this->_fwd[$field] = $fwd;
  }

  /**
   * Mass load mechanism for forward references of an object collection
   *
   * @param self[] $objects      Array of objects
   * @param string $field        Field to load
   * @param string $object_class Restrict to explicit object class in case of meta reference
   * @param bool   $keep_sorted  Keep the same order as the one in $objects
   *
   * @return self[] Loaded collection, null if unavailable, with ids as keys of guids for meta references
   */
  static function massLoadFwdRef($objects, $field, $object_class = null, $keep_sorted = false) {
    if (!count($objects)) {
      return array();
    }

    $object = reset($objects);
    $spec = $object->_specs[$field];
    
    if (!$spec instanceof CRefSpec) {
      trigger_error("Can't mass load not ref '$field' for class '$object->_class'", E_USER_WARNING);
      return null;
    }
    $meta = $spec->meta;
    if ($object_class && !$spec->meta) {
      trigger_error("Mass load with object class is unavailable for non meta ref '$field' in class '$object->_class'", E_USER_WARNING);
      return null;
    }

    // Delegated mass load forward references by meta class then append in global array with guid as keys
    if ($meta && !$object_class) {
      $object_classes = array();
      foreach ($objects as $_object) {
        $object_classes[$_object->$meta] = true;
      }

      $fwd_objects = array();
      foreach (array_keys($object_classes) as $_object_class) {
        // Merge array_values to get rid of non integer keys
        $fwd_objects = array_merge($fwd_objects, array_values(self::massLoadFwdRef($objects, $field, $_object_class)));
      }

      // Final array has guids for keys;
      return array_combine(CMbArray::pluck($fwd_objects, "_guid"), $fwd_objects);
    }

    // No existing class
    if (!self::classExists($spec->class)) {
      return null;
    }

    /** @var self $fwd */
    $class = CValue::first($object_class, $spec->class);

    $fwd = self::getInstance($class);

    // Inactive module
    if (!$fwd->_ref_module) {
      return null;
    }

    // Get the ids
    $fwd_ids = array();
    if ($object_class) {
      foreach ($objects as $_object) {
        if ($_object->$meta == $object_class) {
          $fwd_ids[] = $_object->$field;
        }
      }
    }
    else {
      $fwd_ids = CMbArray::pluck($objects, $field);
    }

    // Trim real ids
    $fwd_ids = array_unique($fwd_ids);
    CMbArray::removeValue("", $fwd_ids);

    // Only run when there's something to look for
    if (!count($fwd_ids)) {
      return array();
    }

    $where[$fwd->_spec->key] = CSQLDataSource::prepareIn($fwd_ids);
    $list = $fwd->loadList($where);

    if (!$keep_sorted) {
      return $list;
    }

    $list_sorted = array();
    foreach ($fwd_ids as $_fwd_id) {
      $list_sorted[$_fwd_id] = $list[$_fwd_id];
    }

    return $list_sorted;
  }

  /**
   * Load all forward references
   *
   * @return void
   */
  function loadAllFwdRefs() {
    foreach ($this->_specs as $field => $spec) {
      $this->loadFwdRef($field);
    }
  }
    
  /**
   * Check whether the object can be deleted.
   * Default behaviour counts for back reference without cascade
   *
   * @return string|null null if ok, error message otherwise
   */
  function canDeleteEx() {
    // Empty object
    if (!$this->_id) {
      return CAppUI::tr("noObjectToDelete") . " " . CAppUI::tr($this->_class);
    }
    
    // Counting backrefs
    $issues = array();
    $this->makeAllBackSpecs();
    foreach ($this->_backSpecs as $backName => &$backSpec) {
      /** @var self $backObject */
      $backObject = new $backSpec->class;
      $backField = $backSpec->field;

      /** @var CRefSpec $fwdSpec */
      $fwdSpec = $backObject->_specs[$backField];
      $backMeta = $fwdSpec->meta;

      // Cas du module non installé
      if (!$backObject->_ref_module) {
        continue;
      }
    
      // Cas de la nullification
      if ($fwdSpec->nullify) {
        continue;
      }
      
      // Cas de la suppression en cascade
      if ($fwdSpec->cascade || $backSpec->cascade) {
        
        // Vérification de la possibilité de supprimer chaque backref
        $backObject->$backField = $this->_id;

        // Cas des meta objects
        if ($backMeta) {
          $backObject->$backMeta = $this->_class;
        }

        $subissues = array();
        $cascadeIssuesCount = 0;
        $cascadeObjects = $backObject->loadMatchingList();
        foreach ($cascadeObjects as $cascadeObject) {
          if ($msg = $cascadeObject->canDeleteEx()) {
            $subissues[] = $msg;
          }
        }
        
        if (count($subissues)) {
          $issues[] = CAppUI::tr("CMbObject-msg-cascade-issues")
            . " " . $cascadeIssuesCount 
            . "/" . count($cascadeObjects) 
            . " " . CAppUI::tr("$fwdSpec->class-back-$backName")
            . ": " . implode(", ", $subissues);
        }
        
        continue;
      }
      
      // Vérification du nombre de backRefs
      if (!$fwdSpec->unlink) {
        if ($backCount = $this->countBackRefs($backName, array(), array(), false)) {
          $issues[] = $backCount 
            . " " . CAppUI::tr("$fwdSpec->class-back-$backName");
        }
      }
    };
    
    $msg = count($issues) ?
      CAppUI::tr("CMbObject-msg-nodelete-backrefs") . ": " . implode(", ", $issues) :
      null;
    
    return $msg;
  }

  /**
   * Default delete method
   *
   * @return null|string null if successful, an error message otherwise
   */
  function delete() {
    // Delete checking
    if (!$this->_purge) {
      // Préparation du log
      $this->loadOldObject();

      if ($msg = $this->canDeleteEx()) {
        return $msg;
      }
    }
    
    // Trigger before event
    $this->notify("BeforeDelete");

    // Deleting backSpecs
    foreach ($this->_backSpecs as $backSpec) {
      /** @var self $backObject */
      $backObject = new $backSpec->class;
      $backField  = $backSpec->field;

      /** @var CRefSpec $fwdSpec */
      $fwdSpec    = $backObject->_specs[$backField];
      $backMeta   = $fwdSpec->meta;
      
      /* Cas du module non installé, 
       * Cas de l'interdiction de suppression, 
       * Cas de l'interdiction de la non liaison des backRefs */
      if (!$backObject->_ref_module) {
        continue; 
      } 
      
      if (!($fwdSpec->cascade || $backSpec->cascade || $fwdSpec->nullify) || $fwdSpec->unlink) {
        continue; 
      }
      
      $backObject->$backField = $this->_id;
      
      // Cas des meta objects
      if ($backMeta) {
        $backObject->$backMeta = $this->_class;
      }

      foreach ($backObject->loadMatchingList() as $object) {      
        // Cas de nullification de la collection
        if ($fwdSpec->nullify) {
          $object->$backField = "";
          if ($msg = $object->store()) {
            return $msg;
          }
        }
        
        // Suppression en cascade
        if ($fwdSpec->cascade) {
          if ($msg = $object->delete()) {
            return $msg;
          }
        }
        
      }
    }
    
    // Actually delete record
    $ds = $this->getDS();
    $result = $ds->deleteObject($this->_spec->table, $this->_spec->key, $this->_id);

    if (!$result) {
      return CAppUI::tr($this->_class) .
        CAppUI::tr("CMbObject-msg-delete-failed") .
        $this->_spec->ds->error();
    }
   
    // Deletion successful
    $this->_id = null;
   
    // Enregistrement du log une fois le delete terminé
    $this->prepareLog();
    $this->doLog();
        
    // Event Handlers
    $this->notify("AfterDelete");

    return $this->_old = null;
  }

  /**
   * Purge an entire object, including recursive back references
   *
   * @return string Store-like message
   */
  function purge() {
    $this->loadAllBackRefs();
    foreach ($this->_back as $backName => $backRefs) {
      foreach ($backRefs as $backRef) {
        /** @var self $backRef */
        $backSpec = $this->_backSpecs[$backName];
        if ($backSpec->_notNull || $backSpec->_purgeable || $backSpec->_cascade || $backSpec->cascade) {
          if ($msg = $backRef->purge()) {
            return $msg;
          }
        }
        else {
          $backRef->{$backSpec->field} = "";
          if ($msg = $backRef->store()) {
            return $msg;
          }
        }
        CAppUI::setMsg("$backRef->_class-msg-delete", UI_MSG_ALERT);
      }
    }

    // Make sure delete won't log and won't can delete
    $this->_purge = "1";
    return $this->delete();
  }

  /**
   * Retrieve seekable specs from object
   *
   * @return CMbFieldSpec[]
   */
  function getSeekables() {
    $seekables = array();
    foreach ($this->_specs as $field => $spec) {
      if (isset($spec->seekable)) {
        $seekables[$field] = $spec;
      }
    }
    return $seekables;
  }

  /**
   * Generic seek method
   *
   * @param string $keywords   Keywords to search
   * @param array  $where      Where statements
   * @param int    $limit      Limit the number of results
   * @param bool   $countTotal Count the totale number of results (like if $limit == infinite)
   * @param array  $ljoin      Left join statements
   * @param array  $order      Order by
   *
   * @todo Change the order of the arguments so that it matches the loadList method
   * @return self[] The first 100 records which fits the keywords
   */
  function seek($keywords, $where = array(), $limit = 100, $countTotal = false, $ljoin = null, $order = null) {
    if (!is_array($keywords)) {
      $regex = '/"([^"]+)"/';

      $keywords = trim($keywords);
      $keywords = str_replace('\\"', '"', $keywords);
      
      if (preg_match_all($regex, $keywords, $matches)) { // Find quoted strings
        $keywords = preg_replace($regex, "", $keywords); // ... and remove them
      }
      
      $keywords = preg_split('/\s+/', $keywords);
      
      // If there are quoted strings
      if (isset($matches[1])) {
        $quoted_strings = $matches[1];
        foreach ($quoted_strings as &$_quoted) {
          $_quoted = str_replace(" ", "_", $_quoted);
        }
        $keywords = array_merge($quoted_strings, $keywords);
      }
      
      $keywords = array_filter($keywords);
    }
    
    $seekables = $this->getSeekables();

    $query = "FROM `{$this->_spec->table}` ";
    
    if ($ljoin && count($ljoin)) {
      foreach ($ljoin as $table => $condition) {
        $query .= "\nLEFT JOIN `$table` ON $condition";
      }
    }
    
    $noWhere = true;
    
    $query .= " WHERE 1";
    
    // Add specific where clauses
    if ($where && count($where)) {
      $noWhere = false;
      foreach ($where as $col => $value) {
        if (is_string($col)) {
          $col = str_replace('.', '`.`', $col);
        }
        if (is_numeric($col)) {
          $query .= " AND ($value)";
        }
        else {
          $query .= " AND (`$col` $value)";
        }
      }
    }

    // Add seek clauses
    if (count($keywords) && count($seekables)) {
      foreach ($keywords as $index => $keyword) {
        $query .= "\nAND (0";
        foreach ($seekables as $field => $spec) {
          // Note: a switch won't work du to boolean true value
          if ($spec->seekable === "equal" AND $index == 0) {
            $query .= "\nOR `{$this->_spec->table}`.`$field` = '$keyword'";
          }
          if ($spec->seekable === "begin" AND $index == 0) {
            $query .= "\nOR `{$this->_spec->table}`.`$field` LIKE '$keyword%'";
          }
          if ($spec->seekable === "end" AND $index == 0) {
            $query .= "\nOR `{$this->_spec->table}`.`$field` LIKE '%$keyword'";
          }
          if ($spec->seekable === true OR $index != 0) {
            if ($spec instanceof CRefSpec && $spec->class !== $this->_class) {
              $class = $spec->class;

              if (isset($spec->meta)) {
                $class = $this->{$spec->meta};
              }

              /** @var self $object */
              $object = new $class;
              $objects = $object->seek($keywords);

              if (count($objects)) {
                $ids = implode(',', array_keys($objects));
                $query .= "\nOR `{$this->_spec->table}`.`$field` IN ($ids)";
              }
            }
            else {
              $query .= "\nOR `{$this->_spec->table}`.`$field` LIKE '%$keyword%'";
            }
          }
        }
        
        $query .= "\n)";
      }
    }
    else if ($noWhere) {
      $query .= "\nAND 0";
    }
    
    $this->_totalSeek = null;
    
    if ($countTotal) {
      $ds = $this->_spec->ds;
      $result = $ds->query("SELECT COUNT(*) AS _total $query");
      $line = $ds->fetchAssoc($result);
      $this->_totalSeek = $line["_total"];
      $ds->freeResult($result);
    }
    
    $query .= "\nORDER BY";
    if ($order) {
      $query .= "\n $order";
    }
    else {
      foreach ($seekables as $field => $spec) {
        $query .= "\n`$field`,";
      }
      $query .= "\n `{$this->_spec->table}`.`{$this->_spec->key}`";
    }
    
    if ($limit) {
      $query .= "\n LIMIT $limit";
    }

    return $this->loadQueryList("SELECT `{$this->_spec->table}`.* $query");
  }
  
  /**
   * Returns a list of objects for autocompleted fields
   * 
   * @param string $keywords Autocomplete seek fields
   * @param array  $where    Where statements
   * @param int    $limit    Limit the number of results
   * @param array  $ljoin    Left join statements
   * @param array  $order    Order by
   *
   * @return self[]
   */
  function getAutocompleteList($keywords, $where = null, $limit = null, $ljoin = null, $order = null) {
    return $this->seek($keywords, $where, $limit, false, $ljoin, $order);
  }

  /**
   * Returns a list of objects for autocompleted fields
   *
   * @param int    $permType Type of permission
   * @param string $keywords Autocomplete seek fields
   * @param array  $where    Where statements
   * @param int    $limit    Limit the number of results
   * @param array  $ljoin    Left join statements
   * @param array  $order    Order by
   *
   * @return self[]
   */
  function getAutocompleteListWithPerms(
      $permType = PERM_READ, $keywords = null, $where = null, $limit = null, $ljoin = null, $order = null
  ) {
    // Filter with permission
    if (!$permType) {
      return $this->getAutocompleteList($keywords, $where, $limit, $ljoin, $order);
    }

    // Load with no limit
    $list = $this->getAutocompleteList($keywords, $where, null, $ljoin, $order);
    self::filterByPerm($list, $permType);

    // We simulate the MySQL LIMIT
    if ($limit) {
      $list = CRequest::artificialLimit($list, $limit);
    }

    return $list;
  }

  /**
   * Load similar objects, ie unexpectetly having the same first unique tuple
   *
   * @param string[] $values Unique tuple values
   *
   * @return CStoredObject[]|null Similar objects, null when unavailble
   */
  function getSimilar($values) {
    $spec = $this->_spec;
    
    if (empty($spec->uniques)) {
      return null;
    }
    
    // @todo only the first unique is used
    $first_unique = reset($spec->uniques);
    
    if (empty($first_unique)) {
      return null;
    }
    
    $where = array();
    foreach ($first_unique as $field_name) {
      if (!array_key_exists($field_name, $values)) {
        continue;
      }
      
      $where[$field_name] = $spec->ds->prepare("=%", $values[$field_name]);
    }
    
    return $this->loadList($where);
  }

  /**
   * Get IDs from similar object, based on unique tuples
   *
   * @return integer[]|null
   */
  function getSimilarIDs() {
    $spec = $this->_spec;

    if (empty($spec->uniques)) {
      return null;
    }

    $first_unique = reset($spec->uniques);

    if (empty($first_unique)) {
      return null;
    }

    $where = array();
    foreach ($first_unique as $field_name) {
      $where[$field_name] = $spec->ds->prepare("=%", $this->$field_name);
    }

    return $this->loadIds($where);
  }

  /**
   * Load object view information
   *
   * @return void
   */
  function loadView() {
    $this->loadAllFwdRefs();
    $this->canDo();
  }


  /**
   * Get the object's data source object
   *
   * @return CSQLDataSource The datasource object
   */
  function getDS(){
    return $this->_spec->ds;
  }

  /**
   * Create new QueryBuilder
   *
   * @return CSQLQuery
   */
  function query() {
    $spec = $this->getSpec();

    /** @fixme Really a CSQLDataSource, not a CPDODataSource ? */
    return new CSQLQuery($this->getDS(), $spec->table, $spec->key, $this->_class);
  }

  /**
   * To call queryBuilder in static
   *
   * @return mixed
   */
  /*
  static function q() {
    $instance = new static;

    return $instance->queryBuilder();
  }
  */
}
