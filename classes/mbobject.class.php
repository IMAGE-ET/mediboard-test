<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("request");
CAppUI::requireSystemClass("mbFieldSpecFact");
CAppUI::requireSystemClass("mbObjectSpec");
 
/**
 * Class CMbObject 
 * @abstract Adds Mediboard abstraction layer functionality
 */
class CMbObject {
  static $useObjectCache = true;
  static $objectCount = 0;
  static $objectCache = array();
  static $cachableCounts = array();
  static $handlers = null;
  
  /**
   * @var string The object's class name
   */
  var $_class_name    = null; 
  
  /**
   * @var integer The object ID
   */
  var $_id            = null;
  
  /**
   * @var string The object GUID (object_class-object_id)
   */
  var $_guid          = null;
  
  /**
   * @var string The universal object view
   */
  var $_view          = '';
  
  /**
   * @var string The universal object shortview
   */
  var $_shortview     = '';
  
  /**
   * @var CCanDo
   */
  var $_can           = null;
  
  var $_canRead       = null; // read permission for the object
  var $_canEdit       = null; // write permission for the object
  var $_external      = null; // true if object is has remote ids
  var $_locked        = null; // true if object is locked

  var $_view_template          = null; // view template path
  var $_complete_view_template = null; // complete view template path

  /**
   * @var CMbObjectSpec The class specification
   */
  var $_spec          = null;    // Class specification
  var $_props         = array(); // Properties specifications as string
  var $_specs         = array(); // Properties specifications as objects
  var $_backProps     = array(); // Back reference specification as string
  var $_backSpecs     = array(); // Back reference specification as objects
  var $_configs       = array(); // Object configs

  static $spec          = array();
  static $props         = array();
  static $specs         = array();
  static $backProps     = array();
  static $backSpecs     = array();
  
  static $module_name   = array();
  
  var $_aides         = array(); // Aides à la saisie
  var $_aides_new     = array(); // Nouveau tableau des aides (sans hierarchie)
  var $_nb_files_docs = null;
  
  /**
   * References
   */
  var $_back           = null; // Back references collections
  var $_count          = null; // Back references counts
  var $_fwd            = null; // Forward references
  var $_history        = null; // Array representation of the object's evolution
  var $_ref_module     = null; // Parent module
  var $_ref_logs       = null; // history of the object
  var $_ref_first_log  = null;
  var $_ref_last_log   = null;
  var $_ref_last_id400 = null;
  var $_ref_notes      = null; // Notes
  var $_ref_documents  = array(); // Documents
  var $_ref_files      = array(); // Fichiers
  var $_ref_affectations_personnel  = null;
  var $_ref_object_configs = null; // Object configs
  
  /**
   * @var CMbObject The object in database
   */
  var $_old            = null;
  
  // Behaviour fields
  var $_merging           = null;
  var $_purge             = null;
  var $_forwardRefMerging = null;
  
  function __construct() {
    return $this->CMbObject();
  }
  
  function __toString() {
  	return $this->_view;
  }
  
  function CMbObject() {
    $class = get_class($this);
   
    $in_cache = isset(self::$spec[$class]);

    if (!$in_cache) {
      self::$spec[$class] = $this->getSpec();
      self::$spec[$class]->init();
      
      global $classPaths;
      if (isset($classPaths[$class])) {
        $module = self::getModuleName($classPaths[$class]);
      }
      else {
	      $reflection = new ReflectionClass($class);
	      $module = self::getModuleName($reflection->getFileName());
      }
      self::$module_name[$class] = $module;
    }
    
    $this->_class_name = $class;
    $this->_spec       =& self::$spec[$class];
    $this->loadRefModule(self::$module_name[$class]);
    
    if ($key = $this->_spec->key) {
      $this->_id =& $this->$key;
    }
    
    if (!$in_cache) {
      self::$props[$class] = $this->getProps();
      $this->_props =& self::$props[$class];

      self::$specs[$class] = $this->getSpecs();
      $this->_specs =& self::$specs[$class];
      
      self::$backProps[$class] = $this->getBackProps();
      $this->_backProps =& self::$backProps[$class];

      // Not prepared since it depends on many other classes
      // Has to be done as a second pass
      self::$backSpecs[$class] = array(); 

    }

    $this->_props     =& self::$props[$class];
    $this->_specs     =& self::$specs[$class];
    $this->_backProps =& self::$backProps[$class];
    $this->_backSpecs =& self::$backSpecs[$class];
    
    $this->_guid = "$this->_class_name-none";
  }
  
  private static function getModuleName($path) {
    if ("classes" === basename($path = dirname($path))) {
      $path = dirname($path);
    }
    return basename($path);
  }
  
  /**
   * Staticly build object handlers array
   * @return void
   */
  private static final function makeHandlers() {
    if (self::$handlers) {
      return;
    }
    // Static initialisations
    self::$handlers = array();
    foreach (CAppUI::conf("object_handlers") as $handler => $active) {
      if ($active) {
        self::$handlers[] = new $handler;
      }
    }
  }

  /**
   * Initialize object specification
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    return new CMbObjectSpec();
  }
  
  /**
   * Returns the field's formatted value
   * @return string The field's formatted value
   */
  function getFormattedValue($field, $options = array()) {
  	return $this->_specs[$field]->getValue($this, new CSmartyDP, $options);
  }
  
  /**
   * Chargement des notes sur l'objet
   */
  function loadRefsNotes($perm = PERM_READ) {
    $this->_ref_notes = array();
	  $this->_high_notes = false;
    
    if ($this->_id) {
      $this->_ref_notes = CNote::loadNotesForObject($this, $perm);
      foreach($this->_ref_notes as $_note) {
        if ($_note->degre === "high") {
	        $this->_high_notes = true;
	        break;
        }
      }
    }

    return count($this->_ref_notes);
  }

  /**
   * Load files for object
   * @return int file count
   */
  function loadRefsFiles() {
  	if (!$this->_id) return;
    $file = new CFile();
    if ($file->_ref_module) {
      $this->_ref_files = CFile::loadFilesForObject($this);
      return count($this->_ref_files);
    }
  }

  /**
   * Load documents for object
   * @return int document count
   */
  function loadRefsDocs() {
  	if (!$this->_id) return;
    $document = new CCompteRendu();

    if ($document->_ref_module) {
      $document->object_class = $this->_class_name;
      $document->object_id    = $this->_id;
      $this->_ref_documents = $document->loadMatchingList("nom");

      foreach($this->_ref_documents as $_doc) {
        if (!$_doc->canRead()){
           unset($this->_ref_documents[$_doc->_id]);
        }
      }
      return count($this->_ref_documents);
    }
  }
  
  /**
   * Load documents and files for object
   * @return int document + files count
   */
  function loadRefsDocItems() {
  	$nb_files = $this->loadRefsFiles();
    $nb_docs  = $this->loadRefsDocs();
    $this->_nb_files_docs = $nb_docs + $nb_files;
  }
  
  function countDocs() {
    return $this->countBackRefs("documents");
  }
  
  function countFiles(){
    return $this->countBackRefs("files");
  }
  
  function countDocItems($permType = null) {
    $this->_nb_files_docs = $permType ? 
      $this->countDocItemsWithPerm($permType) : 
      $this->countFiles() + $this->countDocs();
    return $this->_nb_files_docs;
  }
  
  function countDocItemsWithPerm($permType = PERM_READ){
    $this->loadRefsFiles();
    if ($this->_ref_files) {
      self::filterByPerm($this->_ref_files, $permType);
    }
    
    $this->loadRefsDocs();
    if ($this->_ref_documents) {
      self::filterByPerm($this->_ref_documents, $permType);
    }
    
    return count($this->_ref_files) + count($this->_ref_documents);
  }
  
  /**
   * Load the object database version
   */
  function loadOldObject() {
    if (!$this->_old) {
      $this->_old = new $this->_class_name;
      $this->_old->load($this->_id);
    }
  }
  
  /**
   * Check wether a field has been modified or not
   * @param field string Field name
   * @param value mixed Check if modified to given value.
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
      return round($this->$field, 2) != round($this->_old->$field, 2);
    }
    
    // Check against a specific value
    if ($value !== null && $this->$field != $value) {
      return false;
    }
    
    // Has it finally been modified ?
    return $this->$field != $this->_old->$field;
  }
  
  /**
   * Complete fields with base value if missing
   * @param [...] string Field names or an array of field names
   */
  function completeField() {
  	if (!$this->_id) return;
  	
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
   * Chargement du dernier identifiant id400
   * @param : $tag string Tag à utiliser comme filtre, 
   */
  function loadLastId400($tag = null) {
    $id400 = new CIdSante400();
    if($id400->_ref_module) {
      $id400->loadLatestFor($this, $tag);
      $this->_ref_last_id400 = $id400;
    }
  }
  
  /**
   * Permission generic check
   * @param $permType Const enum Type of permission : PERM_READ|PERM_EDIT|PERM_DENY
   * @return boolean
   */
  function getPerm($permType) {
    return CPermObject::getPermObject($this, $permType);
  }
  
  function canRead() {
    return $this->_canRead = $this->getPerm(PERM_READ);
  }
  
  function canEdit() {
    return $this->_canEdit = $this->getPerm(PERM_EDIT);
  }

  function canDo() {
    $canDo = new CCanDo;
    $canDo->read  = $this->canRead();
    $canDo->edit  = $this->canEdit();
    return $this->_can = $canDo;
  }
  
  function loadListWithPerms($permType = PERM_READ, $where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    // Filter with permission
    if (!$permType) {
      $this->_totalWithPerms = $this->countList($where, null, null, $group, $leftjoin);
      return $this->loadList($where, $order, $limit, $group, $leftjoin);
    }
    else {
      $list = $this->loadList($where, $order, null, $group, $leftjoin);
    }
    
    self::filterByPerm($list, $permType);
    
    $this->_totalWithPerms = count($list);
    
    // We simulate the MySQL LIMIT
    if ($limit) {
      preg_match("/(?:(\d+),)?(\d+)/", $limit, $matches);
      $offset = intval($matches[1]);
      $length = intval($matches[2]);
      $list = array_slice($list, $offset, $length, true);
    }
    
    return $list;
  }
  
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
   * Bind an object with an array
   * @param array $hash associative array of values to match with
   * @param bool $doStripSlashes true to strip slashes
   */
  function bind($hash, $doStripSlashes = true) {
    bindHashToObject($doStripSlashes ? stripslashes_deep($hash) : $hash, $this);
    return true;
  }
  
  /** 
   * Loads an object by its idendifier
   * @param integer $id [optional] The object's identifier
   * @return CMbObject|boolean The loaded object if found, false otherwise
   */
  function load($id = null) {
  	if ($id) {
      $this->_id = $id;
    }

    if (!$this->_id || !$this->_spec->table || !$this->_spec->key) {
      return false;
    }
    
    $sql = "SELECT * FROM `{$this->_spec->table}` WHERE `{$this->_spec->key}` = '$this->_id'";
    
    $object = $this->_spec->ds->loadObject($sql, $this);
    
    /*
    if (!$object && CModule::getInstalled("dPsante400")) {
      $idex = new CIdSante400;
      $idex->object_class = $this->_class_name;
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
	 * Load all objects for given identifiers
	 * @params array $ids list of identifiers
   * @return array list of objects
	 */
	function loadAll($ids) {
		$where[$this->_spec->key] = CSQLDataSource::prepareIn($ids);
		return $this->loadList($where);
	}
  
  /**
   * Register the object into cache
   * @return void
   */
  private final function registerCache() {
    if (!self::$useObjectCache) return;
    
    self::$objectCount++;
    
    // Statistiques sur cache d'object
    if (isset(self::$objectCache[$this->_class_name][$this->_id])) {
      if (!isset(self::$cachableCounts[$this->_class_name])) {
        self::$cachableCounts[$this->_class_name] = 0;
      }
      
      self::$cachableCounts[$this->_class_name]++;
    }
	
	  self::$objectCache[$this->_class_name][$this->_id] =& $this;
  }
  
  /**
   * Clears the internal CMbObject cache
   * @return void
   */
  public function clearCache(){
    self::$objectCount = 0;
    self::$cachableCounts = array();
    self::$objectCache = array();
  }
  
  /**
   * Retrieve an already registered object from cache if available,
   * performs a standard load otherwise
   * @param integer $id The actual object identifier
   * @return CMbObject the retrieved object
   */
  function getCached($id) {
    if (isset(self::$objectCache[$this->_class_name][$id])) {
      return self::$objectCache[$this->_class_name][$id];
    }
    
    $this->load($id);
    return $this;
  }
  
  /**
   * Loads the first object matching defined properties
   * @param array|string $order Order SQL statement
   * @param array|string $group Group by SQL statement
   * @param array $leftjoin Left join SQL statement collection
   * @return integer The found object's ID
   */
  function loadMatchingObject($order = null, $group = null, $leftjoin = null) {
    $request = new CRequest;
    $request->addLJoin($leftjoin);
    $request->addGroup($group);
    $request->addOrder($order);

    $this->updateDBFields();
    $db_fields = $this->getDBFields();
    foreach($db_fields as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    
    $this->loadObject($request->where, $request->order, $request->group, $request->ljoin);
    return $this->_id;
  }
  
  /**
   * Loads the list of objects matching the $this properties
   * @param array|string $order Order SQL statement
   * @param string $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array $leftjoin Left join SQL statement collection
   * @return array The list of objects
   */
  function loadMatchingList($order = null, $limit = null, $group = null, $leftjoin = null) {
    $request = new CRequest;
    $request->addLJoin($leftjoin);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $this->updateDBFields();
    $db_fields = $this->getDBFields();
    foreach($db_fields as $key => $value) {
      if ($value !== null) {        
        $request->addWhereClause($key, "= '$value'");
      }
    }
    
    return $this->loadList($request->where, $request->order, $request->limit, $request->group, $request->ljoin);
  }
  
  /**
   * Size of the list of objects matching the $this properties
   * @param array|string $order Order SQL statement
   * @param string $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array $leftjoin Left join SQL statement collection
   * @return integer The count
   */
  function countMatchingList($order = null, $limit = null, $group = null, $leftjoin = null) {
    $request = new CRequest;
    $request->addLJoin($leftjoin);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $this->updateDBFields();
    $db_fields = $this->getDBFields();
    foreach($db_fields as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    return $this->countList($request->where, $request->order, $request->limit, $request->group, $request->ljoin);
  }
  
  /**
   * Loads the first object matching the query
   * @param array|string $order Order SQL statement
   * @param string $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array $leftjoin Left join SQL statement collection
   * @return boolean True if the object was found
   */
  function loadObject($where = null, $order = null, $group = null, $leftjoin = null) {
    $list = $this->loadList($where, $order, '0,1', $group, $leftjoin);

    if ($list)
    foreach($list as $object) {
      $db_fields = $object->getDBFields();
      foreach($db_fields as $key => $value) {
        $this->$key = $value;
      }
      $this->updateFormFields();
      return true;
    }
    return false;
  }
  
  /**
   * Object list by a request constructor
   * @param array|string $order Order SQL statement
   * @param string $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array $leftjoin Left join SQL statement collection
   * @param boolean $forceindex Add the forceindex SQL statement
   * @return CMbObject[] List of found objects, null if module is not installed
   */
  function loadList($where = null, $order = null, $limit = null, $group = null, $leftjoin = null, $forceindex = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
  	$request = new CRequest();
  	$request->addForceIndex($forceindex);
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);
    
    return $this->loadQueryList($request->getRequest($this));
  }
  
  /**
   * Object list for a given group
   * @param array|string $order Order SQL statement
   * @param string $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array $leftjoin Left join SQL statement collection
   * @param boolean $forceindex Add the forceindex SQL statement
   * @return CMbObject[] List of found objects, null if module is not installed
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
  	if(property_exists($this, "group_id")) {
      // Filtre sur l'établissement
      $g = CGroups::loadCurrent();
      $where["group_id"] = "= '$g->_id'";
  	}
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  /**
   * Object list by a request constructor
   * @param $where array Array of where clauses
   * @param $order array Array of order fields
   * @param $limit string MySQL limit clause
   * @param $group array Array of group by clauses
   * @param $leftjoin array Array of left join clauses
   * @return array[CMbObject] List of found objects, null if module is not installed
   */
  function loadIds($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $ds = $this->_spec->ds;
    return $ds->loadColumn($request->getIdsRequest($this));
  }

  /**
   * Object count of a list by a request constructor
   */
  function countList($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
  	$request = new CRequest();
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $ds = $this->_spec->ds;
    return $ds->loadResult($request->getCountRequest($this));
  }
  
  /*
   * Object count of a multiple list by a request constructor
   */
  function countMultipleList($where = null, $order = null, $limit = null, $group = null, $leftjoin = null, $fields = array()) {
    if (!$this->_ref_module) {
      return null;
    }
    
  	$request = new CRequest();
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $ds = $this->_spec->ds;
    return $ds->loadList($request->getCountRequest($this, $fields));
  }
  
  function loadListByReq(CRequest $request) {
  	return $this->loadQueryList($request->getRequest($this));
  }
  
  /**
   * return an array of objects from a SQL SELECT query
   * class must implement the Load() factory, see examples in Webo classes
   * @note to optimize request, only select object oids in $sql
   */
  function loadQueryList($sql) {
  	$ds = $this->_spec->ds;
    $cur = $ds->exec($sql);
    $list = array();
    while ($row = $ds->fetchAssoc($cur)) {
      $newObject = new $this->_class_name;
      $newObject->bind($row, false);
      $newObject->checkConfidential();
      $newObject->updateFormFields();
      $newObject->registerCache();
      $list[$newObject->_id] = $newObject;
    }

    $ds->freeResult($cur);
    return $list;
  }
  
  /**
   * Update the form fields from the DB fields
   */

  function updateFormFields() {
    $this->_guid = "$this->_class_name-$this->_id";
	  $this->_view = CAppUI::tr($this->_class_name) . " " . $this->_id;
    $this->_shortview = "#$this->_id";
    if ($module = $this->_ref_module) {
      $path = "$module->mod_name/templates/$this->_class_name";
	    $this->_view_template          = "{$path}_view.tpl";
	    $this->_complete_view_template = "{$path}_complete.tpl";
    }
  }
  
  /**
   * Build and load an object with a given GUID
   * @param guid $guid
   * @param bool $cached Use cache 
   * @return CMbObject Loaded object, null if inconsistent Guid 
   */
  static function loadFromGuid($guid, $cached = false) {
    list($class, $id) = explode('-', $guid);
    if ($class) {
	    $object = new $class;
	    
			if ($id) {
        return $cached ? $object->getCached($id) : $object->load($id);
			}
			
			return $object;
    }
  }
  
  /**
   * Load object view information 
   */
  function loadView() {
  	$this->loadRefsNotes();
    $this->loadAllFwdRefs();
  }
  
  /**
   * Load complete object view information 
   */
  function loadComplete() {
    $this->loadRefsNotes();
    $this->loadRefs();
  }
  
  /**
   * References loaders
   */
  function loadRefs() {
    if ($this->_id){  
      $this->loadRefsBack();
      $this->loadRefsFwd();
    }
    
    return $this->_id;
  }

  function loadRefsBack() {
    $this->loadExternal();
  }

  function loadRefsFwd() {
  }
  
  function loadExternal() {
    $this->_external = $this->countBackRefs("identifiants");
  }

  /**
   * Nullify object properties that are empry strings
   */
  function nullifyEmptyFields() {
    foreach ($this->getDBFields() as $propName => $propValue) {
      if ($propValue === "") {
        $this->$propName = null;
      }
    }
  }
  
  /**
   * Extends object properties with target object (of the same class) properties
   * @param CMbObject $mbObject object to extend with 
   */
  function extendsWith(CMbObject $mbObject) {
    $targetClass = $mbObject->_class_name;
    $thisClass = $this->_class_name;
    if ($targetClass !== $thisClass) {
      trigger_error(printf("Target object has not the same class (%s) as this (%s)", $targetClass, $thisClass), E_USER_WARNING);
      return;
    }
    
    foreach ($mbObject->getValues() as $propName => $propValue) {
      if ($propValue !== null) {
        $this->$propName = $propValue;
      }
    }
  }
  
  function loadRefModule($name) {
    $this->_ref_module = CModule::getActive($name);
  }

  /**
   * Repair all non checking properties when possible
   * @return null|array if the object is ok an array of message for repaired fields
   */
  function repair() {
    $repaired = array();
    $properties = get_object_vars($this);
    foreach ($this->getValues() as $name => $value) {
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
   * @return string Store-like message
   */
  function check() {
    $debug = CAppUI::conf("debug");
    
    $msg = "";
    
    // Property level checking
    foreach($this->_props as $propName => $propSpec) {
      if ($propName[0] !== '_') {
        if (!property_exists($this, $propName)) {
          trigger_error("La spécification cible la propriété '$propName' inexistante dans la classe '$this->_class_name'", E_USER_WARNING);
        } 
        else {
          $propValue =& $this->$propName;
          if(($propValue !== null) || (!$this->_id)) {
            $msgProp = $this->checkProperty($propName);
            
            $value = CMbString::truncate($propValue);
            $debugInfo = $debug ? "(val:\"$value\", spec:\"$propSpec\")" : "(valeur: \"$value\")";
            $fieldName = CAppUI::tr("$this->_class_name-$propName");
            $msg .= $msgProp ? "<br/> - <strong title='$propName'>$fieldName</strong> : $msgProp $debugInfo" : null;
          }
        }
      }
    }
    
    if ($this->_merging) return $msg;

    // Class level unique checking
    foreach ($this->_spec->uniques as $unique => $propNames) {
      $other = new $this->_class_name;
      
      foreach ($propNames as $propName) {
  	    $this->completeField($propName);
  	    $other->$propName = addslashes($this->$propName);
      }
      
	    $other->loadMatchingObject();
	
	    if ($other->_id && $this->_id != $other->_id) {
	      return CAppUI::tr("$this->_class_name-failed-$unique");
	    }
    }
    
    // Class-level xor checking
    foreach ($this->_spec->xor as $xor => $propNames) {
      $n = 0;
      $fields = array();
      foreach($propNames as $propName) {
        $this->completeField($propName);
        $fields[] = CAppUI::tr("$this->_class_name-$propName");
        if ($this->$propName) $n++;
      }
  
      if ($n != 1) {
        return CAppUI::tr("$this->_class_name-xorFailed-$xor")." (".implode(", ", $fields).")";
      }
    }
    
    return $msg;
  }

  /**
   * Update the form fields from the DB fields
   */
  function updateDBFields() {
    $specs = $this->_specs;
    $fields = $this->getDBFields();
    
    foreach ($fields as $field => $value) {
      if ($value !== null) {
        $this->$field = $specs[$field]->trim($value);
      }
    }
  }
  
  /**
   * Prepare the user log before object persistence (store or delete)
   */
  private function prepareLog() {
    // If the object is not loggable
    if (!$this->_spec->loggable || $this->_purge) {
      return;
    }

    // Find changed fields
    $fields = array();
    $db_fields = $this->getDBFields();
    
    foreach ($db_fields as $propName => $propValue) {
      if ($this->fieldModified($propName)) {
        $fields[] = $propName;
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
    	return;
    }
   
    if ($type === "store" || $type === "merge") {
    	$old_values = array();
    	foreach($fields as $_field) {
    		$_spec = $this->_specs[$_field];
    		if ($_spec instanceof CTextSpec ||
            $_spec instanceof CHtmlSpec ||
            $_spec instanceof CXmlSpec ||
            $_spec instanceof CPhpSpec) {
    			continue;
    		}
    		$old_values[$_field] = utf8_encode($old->$_field);
    	}
    	$extra = json_encode($old_values);
    }
    
    // TODO: supprimer ces lignes
    $system_version = explode(".", CModule::getInstalled("system")->mod_version);
    if ($system_version[0] == 1 && $system_version[1] == 0 && $system_version[2] < 4){
      return;	
    }
    
    $address = get_remote_address();
    
    $log = new CUserLog;
    $log->user_id = CAppUI::$instance->user_id;
    $log->object_id = $object_id;
    $log->object_class = $this->_class_name;
    $log->ip_address = $address["client"] ? inet_pton($address["client"]) : null;
    $log->type = $type;
    $log->_fields = $fields;
    $log->date = mbDateTime();
    $log->extra = $extra;
    
    $this->_ref_last_log = $log;
  }
  
  /**
   * Prepare the user log before object persistence (store or delete)
   */
  private function doLog() {
    // Aucun log à produire (non loggable, pas de modifications, etc.)
    if (!$this->_ref_last_log) {
      return;
    }
    
    $this->_ref_last_log->store();
  }
  
  /**
   * Inserts a new row if id is zero or updates an existing row in the database table
   * @param boolean $checkobject check values before storing if true (default)
   * @return null|string null if successful otherwise returns and error message
   */
  function store() {
    // Properties checking
    $this->updateDBFields();

    // Préparation du log
    $this->loadOldObject();
    
    if ($msg = $this->check()) {
      return CAppUI::tr($this->_class_name) . 
        CAppUI::tr("CMbObject-msg-check-failed") .
        CAppUI::tr($msg);
    }
    
    // Trigger before event
    $this->onBeforeStore();

    $spec = $this->_spec;
		
    // DB query
    if ($this->_old->_id) {
      $ret = $spec->ds->updateObject($spec->table, $this, $spec->key, $spec->nullifyEmptyStrings);
    } else {
      $keyToUpdate = $spec->incremented ? $spec->key : null;
      $ret = $spec->ds->insertObject($spec->table, $this, $keyToUpdate);
    }

    if (!$ret) {
      return "$this->_class_name::store failed <br />" . $spec->ds->error();
    }

    // Load the object to get all properties
    $this->load();
    
    // Enregistrement du log une fois le store terminé
    $this->prepareLog();
    $this->doLog();
        
    // Trigger event
    $this->onAfterStore();

    $this->_old = null;
    return null;
  }

  /**
   * Trigger before store event for handlers
   * @return void
   */
  function onBeforeStore() {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onBeforeStore($this);
    }
  }
  
  /**
   * Trigger after store event for handlers
   * @return void
   */
  function onAfterStore() {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onAfterStore($this);
    }
  }
  
  /**
   * Merges an array of objects
   * @param array An array of CMbObject to merge
   * @param bool $fast Tell wether to use SQL (fast) or PHP (slow but checked and logged) algorithm
   * @return CMbObject
   */
  function merge($objects = array/*<CMbObject>*/(), $fast = false) {
    $alternative_mode = ($this->_id != null);
    
    // If alternative mode and too many objects
    if ($alternative_mode) {
      if (count($objects) > 1) return "mergeAlternativeTooManyObjects";
    }
    else {
      if (count($objects) < 2) return "mergeTooFewObjects";
    }
        
    if ($msg = $this->checkMerge($objects)) return $msg;
    
    // Trigger before event
    $this->onBeforeMerge();
    
    if (!$this->_id && $msg = $this->store()) return $msg;
    
    foreach ($objects as $object) {
      $this->_merging[$object->_id] = $object;
    }
    
    // If external IDs are available, we save old objects' id as external IDs
    // This must not be done after the objects deletion !
    if (CModule::getInstalled("dPsante400")) {
      foreach ($objects as $object) {
        $idex = new CIdSante400;
        $idex->tag = "merged";
        $idex->setObject($this);
        $idex->id400 = $object->_id;
        $idex->last_update = mbDateTime();
        $idex->store();
      }
    }
    
    foreach ($objects as &$object) {
      $msg = $fast ? 
        $this->fastTransferBackRefsFrom($object) :
        $this->transferBackRefsFrom($object);
        
    	if ($msg) return $msg;
    	if ($msg = $object->delete()) return $msg;
    }
    
    // Trigger after event
    $this->onAfterMerge();
    
    return $this->store();
  }
  
  function checkMerge($objects = array/*<CMbObject>*/()) {
    $object_class = null;
    foreach ($objects as $object) {
      if (!$object instanceof CMbObject) {
        return 'mergeNotCMbObject';
      }
      if (!$object->_id) {
        return 'mergeNoId';
      }
      if (!$object_class) {
        $object_class = $object->_class_name;
      }
      else if ($object->_class_name !== $object_class) {
        return 'mergeDifferentType';
      }
    }
    return null;
  }
  
  /**
   * Merges the fields of an array of objects to $this
   * @param $objects An array of CMbObject
   * @return $this or an error
   */
  function mergeDBFields ($objects = array()/*<CMbObject>*/, $getFirstValue = false) {
		if ($msg = $this->checkMerge($objects)) {
			return $msg;
		}
		
    $db_fields = $this->getDBFields();
    $diffs = $db_fields;
    foreach ($diffs as &$diff) $diff = false;
    
    foreach ($objects as &$object) {
      foreach ($db_fields as $propName => $propValue) {
        if ($getFirstValue) {
          if ($this->$propName === null) {
            $this->$propName = $object->$propName;
          }
        }
        else {
          if ($this->$propName === null && !$diffs[$propName]) {
            $this->$propName = $object->$propName;
          }
          else if ($this->$propName != $object->$propName) {
            $diffs[$propName] = true;
            $this->$propName = null;
          }
        }
      }
    }
  }
  
  /**
   * Trigger before merge event for handlers
   * @return void
   */
  function onBeforeMerge() {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onBeforeMerge($this);
    }
  }
  
  /**
   * Trigger after merge event for handlers
   * @return void
   */
  function onAfterMerge() {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onAfterMerge($this);
    }
  }
  
  /**
   * Count number back refreferecing object
   * @param string $backName name the of the back references to count
   * @return int the count null if back references module is not installed
   * @todo Add the missing arguments (the same as loadbackRefs)
   */
  function countBackRefs($backName) {
    if (!$backSpec = $this->makeBackSpec($backName)) {
    	return null;
    }
    $backObject = new $backSpec->class;
    $backField = $backSpec->field;
    
    // Cas du module non installé
    if (!$backObject->_ref_module) {
      return null;
    }

    // Empty object
    if (!$this->_id || !$backObject->_spec->table || !$backObject->_spec->key) {
      return $this->_count[$backName] = 0;
    }
    
    $query = "SELECT COUNT({$backObject->_spec->key}) 
      FROM `{$backObject->_spec->table}`
      WHERE `$backField` = '$this->_id'";

    // Cas des meta objects
    $backSpec =& $backObject->_specs[$backField];
    $backMeta = $backSpec->meta;
    if ($backMeta) {
      $query .= "\nAND `$backMeta` = '$this->_class_name'";
    }
    
    // Comptage des backrefs
    return $this->_count[$backName] = $this->_spec->ds->loadResult($query); 
  }

  /**
   * Load named back reference collection
   * @param $order string|array Order for DB query
   * @param $limit string Limit DB query option
   * @return array[CMbObject] the collection
   */
  function loadBackRefs($backName, $order = null, $limit = null, $group = null, $ljoin = null) {
    if (!$backSpec = $this->makeBackSpec($backName)) {
      return null;
    }

    // Cas du module non installé
    $backObject = new $backSpec->class;
    if (!$backObject->_ref_module) {
      return null;
    }

    $backField = $backSpec->field;
    $fwdSpec =& $backObject->_specs[$backField];
    $backMeta = $fwdSpec->meta;
    
    // Empty object
    if (!$this->_id) {
      return array();
    }

    // Vérification de la possibilité de supprimer chaque backref
    $backObject->$backField = $this->_id;

    // Cas des meta objects
    if ($backMeta) {
      $backObject->$backMeta = $this->_class_name;
    }
    
    return $this->_back[$backName] = $backObject->loadMatchingList($order, $limit, $group, $ljoin);
  }
  
  /**
   * Load the unique back reference for given collection name
   * Will check for uniqueness
   * @param string $backName The collection name
   * @return CMbObject Unique back reference if exist, concrete type empty object otherwise 
   */
  function loadUniqueBackRef($backName) {
    if (null === $backRefs = $this->loadBackRefs($backName)) {
      return null;
    }

    $count = count($backRefs);
    if ($count > 1) {
      trigger_error("'$backName' back reference should be unique (actually $count) for object of class '$this->_view'", E_USER_WARNING);
    }
    
    if (!$count) {
      $backSpec = $this->_backSpecs[$backName];
      return new $backSpec->class;
    }
    
    return reset($backRefs);
  }
  
  /**
   * Load and count all back references collections 
   * @param $limit string Limit DB query option
   * @return void
   */
  function loadAllBackRefs($limit = null) {
    foreach ($this->_backProps as $backName => $backProp) {
      $this->loadBackRefs($backName, null, $limit);
      $this->countBackRefs($backName);
    }
  }
  
  /**
   * Transfer all back refs from given object of same class using unchecked, unlogged SQL queries
   * @param CMbObject $object
   * @return string store-like error message if failed, null if successful
   */
  function fastTransferBackRefsFrom(CMbObject &$object) {
    if (!$this->_id) {
      return;
    }

    foreach ($this->_backProps as $backName => $backProp) {
	    list($backClass, $backField) = explode(" ", $backProp);
	    $backObject = new $backClass;
	
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
	    $backSpec =& $backObject->_specs[$backField];
	    $backMeta = $backSpec->meta;
	    if ($backMeta) {
	      $query .= "\nAND `$backMeta` = '$object->_class_name'";
	    }
	    
	    $this->_spec->ds->exec($query);
    }    
  }
  
  /**
   * Transfer all back refs from given object of same class
   * @param CMbObject $object
   * @return string store-like error message if failed, null if successful
   */
  function transferBackRefsFrom(CMbObject &$object) {
    if (!$object->_id) {
      trigger_error("transferNoId");
    }
    if ($object->_class_name !== $this->_class_name) {
      trigger_error("An object from type '$object->_class_name' can't be merge with an object from type '$this->_class_name'", E_USER_ERROR);
    }
    
    $object->loadAllBackRefs();
    foreach ($object->_back as $backName => $backObjects) {
      if (count($backObjects)) {
        $backSpec = $this->_backSpecs[$backName];
        $backObject = new $backSpec->class;
		    $backField = $backSpec->field;
		    $fwdSpec =& $backObject->_specs[$backField];
		    $backMeta = $fwdSpec->meta;      
	      
        // Change back field and store back objects
	      foreach ($backObjects as $backObject) {
	        // Use a dummy tranferer object to prevent checks on all values
	        $transferer = new $backObject->_class_name;
	        $transferer->_id = $backObject->_id;
          $transferer->$backField = $this->_id;
          $transferer->_forwardRefMerging = true;
          
          // Cas des meta objects
			    if ($backMeta) {
			      $transferer->$backMeta = $this->_class_name;
			    }
          
          if ($msg = $transferer->store()) {
            return $msg;
          }
        }
      }
    }
  }
  
  /**
   * Load named forward reference
   * @return CMbObject concrete loaded object 
   */
  function loadFwdRef($field, $cached = false) {
  	if (isset($this->_fwd[$field]) && $this->_fwd[$field]->_id) {
  		return $this->_fwd[$field];
  	}

    $spec = $this->_specs[$field];
    if ($spec instanceof CRefSpec) {
      $class = $spec->meta ? $this->{$spec->meta} : $spec->class;
			
      if (!$class) 
        return $this->_fwd[$field] = null;
      
      $fwd = new $class;
      
      if ($cached)
        $fwd = $fwd->getCached($this->$field);
      else
  	    $fwd->load($this->$field);

      return $this->_fwd[$field] = $fwd;
    }
  }
  
  /**
   * Load named forward reference
   * @return CMbObject concrete loaded object 
   */
  static function massLoadFwdRef($objects, $field) {
  	if (!count($objects)) {
  		return array();
  	}

    $object = reset($objects);
    $spec = $object->_specs[$field];
		
    if (!$spec instanceof CRefSpec) {
    	trigger_error("Can't mass load not ref '$field' for object class '$object->_class_name'", E_USER_WARNING);
			return;
		}

    if ($spec->meta) {
      trigger_error("Can't mass load (yet!) ref '$field' with meta field '$spec->meta' for object class '$object->_class_name'", E_USER_WARNING);
      return;
    }
		
    // Trim real values
    $fwd_ids = CMbArray::pluck($objects, $field);
    $fwd_ids = array_unique($fwd_ids);
		CMbArray::removeValue("", $fwd_ids);
    if (!count($fwd_ids)) {
      return array();
    }

    $fwd = new $spec->class;
		
		$where[$fwd->_spec->key] = CSQLDataSource::prepareIn($fwd_ids);
		return $fwd->loadList($where);
  }
	  
  /**
   * Load named forward reference
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
   * @return string|null null if ok, error message otherwise
   */
  function canDeleteEx() {
    // Empty object
    if (!$this->_id) {
      return CAppUI::tr("noObjectToDelete") . " " . CAppUI::tr($this->_class_name);
    }
    
    // Counting backrefs
    $issues = array();
    $this->makeAllBackSpecs();
    foreach ($this->_backSpecs as $backName => &$backSpec) {
	    $backObject = new $backSpec->class;
	    $backField = $backSpec->field;
	    $fwdSpec =& $backObject->_specs[$backField];
	    $backMeta = $fwdSpec->meta;

      // Cas du module non installé
      if (!$backObject->_ref_module) {
        continue;
      }
      
      // Cas de la suppression en cascade
      if ($fwdSpec->cascade) {
        
        // Vérification de la possibilité de supprimer chaque backref
        $backObject->$backField = $this->_id;

        // Cas des meta objects
        if ($backMeta) {
          $backObject->$backMeta = $this->_class_name;
        }
        
        $cascadeIssuesCount = 0;
        $cascadeObjects = $backObject->loadMatchingList();
        foreach ($cascadeObjects as $cascadeObject) {
          if ($msg = $cascadeObject->canDeleteEx()) {
            $cascadeIssuesCount++;
          }
        }
        
        if ($cascadeIssuesCount) {
          $issues[] = CAppUI::tr("CMbObject-msg-cascade-issues")
            . " " . $cascadeIssuesCount 
            . "/" . count($cascadeObjects) 
            . " " . CAppUI::tr("$fwdSpec->class-back-$backName");
        }
        
        continue;
      }
      
      // Vérification du nombre de backRefs
      if (!$fwdSpec->unlink) {
        if ($backCount = $this->countBackRefs($backName)) {
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
   * @return null|string null if successful, an error message otherwise
   */
  function delete() {
    // Préparation du log
    $this->loadOldObject();
    
    // Delete checking
    if (!$this->_purge) {
	    if ($msg = $this->canDeleteEx()) {
	      return $msg;
	    }
    }

    // Deleting backSpecs
    //$this->makeAllBackSpecs(); // Already done by canDeleteEx
    foreach ($this->_backSpecs as $backName => $backSpec) {
      $backObject = new $backSpec->class;
      $backField = $backSpec->field;
      $fwdSpec =& $backObject->_specs[$backField];
      $backMeta = $fwdSpec->meta;
      
      /* Cas du module non installé, 
       * Cas de l'interdiction de suppression, 
       * Cas de l'interdiction de la non liaison des backRefs */
      if (!$backObject->_ref_module || !$fwdSpec->cascade || $fwdSpec->unlink) {
        continue; 
      }
      
      $backObject->$backField = $this->_id;
      
      // Cas des meta objects
      if ($backMeta) {
        $backObject->$backMeta = $this->_class_name;
      }

      foreach ($backObject->loadMatchingList() as $object) {
        if ($msg = $object->delete()) {
          return $msg;
        }
      }
    }
    
    // Actually delete record
    $sql = "DELETE FROM {$this->_spec->table} WHERE {$this->_spec->key} = '$this->_id'";
    
    if (!$this->_spec->ds->exec($sql)) {
      return $this->_spec->ds->error();
    }
   
    // Deletion successful
    $this->_id = null;
   
    // Enregistrement du log une fois le delete terminé
    $this->prepareLog();
    $this->doLog();
        
    // Event Handlers
    $this->onAfterDelete();

    return $this->_old = null;
  }

  /**
   * Purge an entire object, including recursive back references
   * @return string Store-like message
   */
  function purge() {
    $this->loadAllBackRefs();
    foreach ($this->_back as $backName => $backRefs) {
      foreach ($backRefs as $backRef) {
        $backSpec = $this->_backSpecs[$backName];
        if ($backSpec->_notNull || $backSpec->_purgeable || $backSpec->_cascade) {
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
        CAppUI::setMsg("$backRef->_class_name-msg-delete", UI_MSG_ALERT);
      }
    }

    // Make sure delete won't log
    $this->_purge = "1";
    return $this->delete();
  }
  
  /**
   * Trigger before delete event for handlers
   * @return void
   */
  function onBeforeDelete() {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onBeforeDelete($this);
    }
  }
  
  /**
   * Trigger after delete event for handlers
   * @return void
   */
  function onAfterDelete() {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onAfterDelete($this);
    }
  }
  
  /**
   * Retrieve seekable specs from object
   *
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
   *  Generic seek method
   *  @todo Change the order of the arguments so that it matches the loadList method
   *  @return the first 100 records which fits the keywords
   */
  function seek($keywords, $where = array(), $limit = 100, $countTotal = false, $ljoin = null, $order = null) {
    if (!is_array($keywords)) {
      $regex = '/"([^"]+)"/';
      
      $keywords = str_replace('\\"', '"', $keywords);
      
      if (preg_match_all($regex, $keywords, $matches)) { // Find quoted strings
        $keywords = preg_replace($regex, "", $keywords); // ... and remove them
      }
      
      $keywords = explode(" ", $keywords);
      
      // If there are quoted strings
      if (isset($matches[1])) {
        $quoted_strings = $matches[1];
        foreach($quoted_strings as &$_quoted) {
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
          $query .= " AND $value";
        }
        else {
          $query .= " AND `$col` $value";
        }
      }
    }
    
    // Add seek clauses
    if (count($keywords) && count($seekables)) {
      foreach($keywords as $keyword) {
        $query .= "\nAND (0";
        foreach($seekables as $field => $spec) {
          // Note: a swith won't work du to boolean trus value
          if ($spec->seekable === "equal") {
            $query .= "\nOR `{$this->_spec->table}`.`$field` = '$keyword'";
          }
          if ($spec->seekable === "begin") {
            $query .= "\nOR `{$this->_spec->table}`.`$field` LIKE '$keyword%'";
          }
          if ($spec->seekable === "end") {
            $query .= "\nOR `{$this->_spec->table}`.`$field` LIKE '%$keyword'";
          }
          if ($spec->seekable === true) {
            if ($spec instanceof CRefSpec) {
            	$object = new $spec->class;
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
    else if ($noWhere){
      $query .= "\nAND 0";
    }
    
    $this->_totalSeek = null;
    
    if ($countTotal) {
      $ds = $this->_spec->ds;
      $result = $ds->query("SELECT COUNT(*) AS _total $query");
      $line = $ds->fetchAssoc($result);
      $this->_totalSeek = $line["_total"];
    }
    
    $query .= "\nORDER BY";
    if($order) {
      $query .= "\n $order";
    } else {
      foreach($seekables as $field => $spec) {
        $query .= "\n`$field`,";
      }
      $query .= "\n `{$this->_spec->table}`.`{$this->_spec->key}`";
    }
    
    if ($limit)
      $query .= "\n LIMIT $limit";

    return $this->loadQueryList("SELECT `{$this->_spec->table}`.* $query");
  }
  
  /**
   * Get DB fields and there values
   * @return array Associative array
   */
  function getDBFields() {
    $result = array();
    $vars = get_object_vars($this);
    foreach($vars as $key => $value) {
      if ($key[0] !== '_') {
        $result[$key] = $value;
      }
    }

    return $result;
  }
  
  /**
   * Escape Value for SQL queries
   * @return void
   */
  function escapeValues() {
  	$values = $this->getValues();
    foreach ($values as $propName => $propValue) {
      if ($propValue) {
        $this->$propName = addslashes($propValue);
      }
    }
  }
  
  /**
   * Unescape Value for SQL queries
   * @return void
   */
  function unescapeValues() {
  	$values = $this->getValues();
    foreach ($values as $propName => $propValue) {
      if ($propValue) {
        $this->$propName = stripslashes($propValue);
      }
    }
  }
  
  /**
   * Get object properties, i.e. having specs
   * @return array Associative array
   */
  function getValues() {
    $result = array();
    
    foreach ($this->_specs as $key => $value) {
      $result[$key] = $this->$key;
    }

    return $result;
  }
  
  /**
   * Get properties specifications as strings
   * @return array
   */
  function getProps() {
    return array (
      "_shortview" => "str",
      "_view" => "str",
      $this->_spec->key => "ref class|$this->_class_name show|0"
    );
  }
  
  /**
   * Get backward reference specifications
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    return array (
		  "alerts"                 => "CAlert object_id",
      "identifiants"           => "CIdSante400 object_id",
      "notes"                  => "CNote object_id",
      "files"                  => "CFile object_id",
      "documents"              => "CCompteRendu object_id",
      "permissions"            => "CPermObject object_id",
      "transmissions"          => "CTransmissionMedicale object_id",
      "logs"                   => "CUserLog object_id",
      "affectations_personnel" => "CAffectationPersonnel object_id",
      "contextes_constante"    => "CConstantesMedicales context_id",
    );
  }
  
  /**
   * Get CSV values for object, i.e. db fields, references excepted
   * @return array Associative array of values
   */
  function getCSVFields() {
		$fields = array();
		foreach ($this->getDBFields() as $key => $value) {
		  if (!$this->_specs[$key] instanceof CRefSpec) {
		    $fields[$key] = $value;
		  }
		}
		return $fields;
  }
  
  function getTemplateClasses(){
    return array($this->_class_name => $this->_id);
  }
  
  /**
   * Convert string back specifications to objet specifications
   * @param string $backName The name of the back reference
   * @return CMbBackSpec The back reference specification, null if undefined
   */
  function makeBackSpec($backName) {
    if (array_key_exists($backName, $this->_backSpecs)) {
      return $this->_backSpecs[$backName];
    }

    if ($backSpec = CMbBackSpec::make($this->_class_name, $backName, $this->_backProps[$backName])) {
      return $this->_backSpecs[$backName] = $backSpec;
    }
  }
  
  /**
   * Makes all the back specs
   * @return nothing
   */
  function makeAllBackSpecs() {
    foreach($this->_backProps as $backName => $backProp) {
    	$this->makeBackSpec($backName);
    }
  }

  /**
   * Converts string specifications to objet specifications
   * Optimized version
   */
  function getSpecs() {
    $spec = array();
    foreach ($this->_props as $propName => $propSpec) {
      $spec[$propName] = CMbFieldSpecFact::getSpec($this, $propName, $propSpec);
    }
    return $spec;
  }
    
  /**
   * Check a property against its specification
   * @param $propName string Name of the property
   * @return string Store-like error message
   */
  function checkProperty($propName) {
    $spec = $this->_specs[$propName];
    return $spec->checkPropertyValue($this);
  }
  
  function checkConfidential($specs = null) {
    if (CAppUI::conf("hide_confidential")) {
      if($specs == null){
        $specs = $this->_specs;
      }
      foreach ($specs as $propName => $propSpec) {
        $propValue =& $this->$propName;
        if ($propValue !== null && $this->_specs[$propName]) {
          $this->_specs[$propName]->checkConfidential($this);
        }
      }
    }
  }
  
  /**
   * Check whether object table is installed
   * @return bool Result
   */
  function isInstalled() {
    return $this->_spec->ds->loadTable($this->_spec->table);    
  }

  /**
   * Charge toutes les aides à la saisie de l'objet pour un utilisateur donné
   *
   * @param ref|CUser $user_id ID de l'utilisateur
   * @param string $keywords Permet de filtrer les aides commançant par le filtre, si non null
   */
  function loadAides($user_id, $keywords = null, $depend_value_1 = null, $depend_value_2 = null, $object_field = null) {
    foreach ($this->_specs as $field => $spec) {
      if (isset($spec->helped)) {
        $this->_aides[$field] = array("no_enum" => null);
      }
    }

    // Chargement de l'utilisateur courant
    $user = new CMediusers();
    $user->load($user_id);
    $user->loadRefFunction();
    
    // Préparation du chargement des aides
    $ds =& $this->_spec->ds;
    
    // Construction du Where
    $where = array();

    $where[] = "(user_id = '$user_id' OR 
      function_id = '$user->function_id' OR 
      group_id = '{$user->_ref_function->group_id}')";
                
    $where["class"]   = $ds->prepare("= %", $this->_class_name);

    if ($depend_value_1){
      $where["depend_value_1"] = " = '$depend_value_1'";
    }
    
    if ($depend_value_2){
      $where["depend_value_2"] = " = '$depend_value_2'";
    }
		
		if($object_field){
		  $where["field"] = " = '$object_field'";
		}
    
    // tri par user puis function puis group (ordre inversé pour avoir ce résultat)
    $order = "group_id, function_id, user_id, depend_value_1, depend_value_2, name, text";
    
    // Chargement des Aides de l'utilisateur
    $aide = new CAideSaisie();
    $aides = $aide->seek($keywords, $where, null, null, null, $order); // TODO: si on veut ajouter un $limit, il faudrait l'ajouter en argument de la fonction loadAides
    $this->orderAides($aides, $depend_value_1, $depend_value_2);
  }
  
  function orderAides($aides, $depend_value_1 = null, $depend_value_2 = null) {
    foreach ($aides as $aide) { 
      $owner = CAppUI::tr("CAideSaisie._owner.$aide->_owner");
      $aide->loadRefOwner();
      
      // si on filtre seulement sur depend_value_1, il faut afficher les resultats suivant depend_value_2
      if ($depend_value_1) {
        $depend_field_2 = $aide->_depend_field_2;
        $depend_2 = CAppUI::tr("$this->_class_name.$aide->_depend_field_2.$aide->depend_value_2");
        if ($aide->depend_value_2){
          $this->_aides[$aide->field][$owner][$depend_2][$aide->text] = $aide->name;
        } 
        else {
          $depend_name_2 = CAppUI::tr("$this->_class_name-$depend_field_2");
          $this->_aides[$aide->field][$owner]["$depend_name_2 non spécifié"][$aide->text] = $aide->name;
        }
        continue;
      }
      
      // ... et réciproquement 
      if ($depend_value_2){
        $depend_field_1 = $aide->_depend_field_1;
        $depend_1 = CAppUI::tr("$this->_class_name.$aide->_depend_field_1.$aide->depend_value_1");
        if ($aide->depend_value_1){    
          $this->_aides[$aide->field][$owner][$depend_1][$aide->text] = $aide->name;
        } 
        else {
          $depend_name_1 = CAppUI::tr("$this->_class_name-$depend_field_1");
          $this->_aides[$aide->field][$owner]["$depend_name_1 non spécifié"][$aide->text] = $aide->name;
        }
        continue;
      }
      
      $this->_aides_all_depends[$aide->field][$aide->depend_value_1][$aide->depend_value_2][$aide->_id] = $aide;
      
      // Ajout de l'aide à la liste générale
      $this->_aides[$aide->field]["no_enum"][$owner][$aide->text] = $aide->name;
    }
    
    $this->_aides_new = $aides;
  }
  
  /**
   * Charge les différents états des champs de l'objet au cours du temps
   *
   */
  function loadHistory() {
  	$this->_history = array();
  	$this->loadLogs();
  	$clone = $this->getDBFields();
  	foreach($this->_ref_logs as $_log) {
      $this->_history[$_log->_id] = $clone;
      $_log->getOldValues();
      foreach($_log->_old_values as $_old_field => $_old_value) {
      	$clone[$_old_field] = $_old_value;
      }
  	}
  }
  
  function loadLogs() {
    $this->_ref_logs = $this->loadBackRefs("logs", "date DESC", 100);

    foreach($this->_ref_logs as &$_log) {
      $_log->loadRefsFwd();
    }
    
		// the first is at the end because of the date order !
    $this->_ref_first_log = end($this->_ref_logs);
    $this->_ref_last_log  = reset($this->_ref_logs);
  }
  
  function loadLastLogForField($fieldName = null){	
    $where["object_id"   ] = " = '$this->_id'";
    $where["object_class"] = " = '$this->_class_name'";
    
  	if ($fieldName){
  	  $where["fields"] = " LIKE '%$fieldName%'";
  	}
  	
    $log = new CUserLog();
    $log->loadObject($where, "date DESC");
  	
  	if ($log->_id){
  	  $log->loadRefsFwd();
  	}
  	return $log;
  }
	
	/**
	 * Check wether object has a log more recent than given hours
	 * @param $nb_hours Number of hours
	 * @return bool
	 */ 
	
  function hasRecentLog($nb_hours = 1) {
    $where["object_id"   ] = "= '$this->_id'";
    $where["object_class"] = "= '$this->_class_name'";
    $where["date"] = "> DATE_ADD(NOW(), INTERVAL -$nb_hours HOUR)";
    $log = new CUserLog();
    return $log->countList($where);
  }
  
  function loadLastLog() {
    $last_log = new CUserLog;
    $last_log->setObject($this);
    $last_log->loadMatchingObject("date DESC");
    $this->_ref_last_log = $last_log;
  }
  
  function loadFirstLog() {
    $last_log = new CUserLog;
    $last_log->setObject($this);
    $last_log->loadMatchingObject("date ASC");
    $this->_ref_first_log = $last_log;
  }
  
  function loadAffectationsPersonnel() {
    // Initialisation
    $personnel = new CPersonnel();
    foreach ($personnel->_specs["emplacement"]->_list as $emplacement) {
      $this->_ref_affectations_personnel[$emplacement] = array();
    }
    
    if (null == $affectations = $this->loadBackRefs("affectations_personnel")) {
      return;
    }
    
    foreach ($affectations as $key => $affectation) {
      $affectation->loadRefPersonnel();
      $affectation->_ref_personnel->loadRefUser();
			$affectation->_ref_personnel->_ref_user->loadRefFunction();
      $this->_ref_affectations_personnel[$affectation->_ref_personnel->emplacement][$affectation->_id] = $affectation;
    }
  }

	/**
	 * This function register all templated properties for the object
	 * Will load as necessary and fill in values
	 * @param $template CTemplateManager
	 */
  function fillTemplate(&$template) {
  }
   
	/**
	 * This function register most important templated properties for the object
	 * Won't register distant properties
	 * Will load as necessary and fill in values
	 * @param $template CTemplateManager
	 **/
  function fillLimitedTemplate(&$template) {
  }
   
  /**
   * Decode all string fields (str, text, html)
   * @return void
   */
  function decodeUtfStrings() {
    foreach($this->_specs as $name => $spec) {
      if (in_array(get_class($spec), array("CStrSpec", "CHtmlSpec", "CTextSpec"))) {
        if (null !== $this->$name) {
          $this->$name = utf8_decode($this->$name);
        }
      }
    }
  }
  
  /**
   * Load object config 
   * @return array contains config of class and/or object
   */
  function loadConfigValues() {
    $object_class = $this->_class_name."Config";
    
    if (!class_exists($object_class)) {
      return;
    }
    
    // Chargement des configs de la classe
    $where = array();
    $where["object_id"]    = " IS NULL";
    $class_config = new $object_class;
    $class_config->loadObject($where);

    // Chargement des configs de l'objet
    $object_config = $this->loadUniqueBackRef("object_configs");
    $class_config->extendsWith($object_config);
    
    $this->_configs = $class_config->getConfigValues();
  }
  
  /**
   * Get value of the object config 
   */
  function getConfigValues() {
    $configs = array();
    
    if (!$this->_id) {
      return $configs;
    }
    
    $fields = $this->getDBFields();
    unset($fields[$this->_spec->key]);
    unset($fields["object_id"]);
    foreach($fields as $_name => $_value) {
      $configs[$_name] = $_value;
    }
    
    return $configs;
  }
  
  /**
   * Set defaults value
   */
  function valueDefaults() {
    $fields = $this->getDBFields();
    $specs  = $this->getSpecs();
    
    foreach($fields as $_name => $_value) {
      $this->$_name = $specs[$_name]->default;
    }
  }
  
  /**
   * Backward references
   */
  function loadRefObjectConfigs() {
    $object_class = $this->_class_name."Config";
    $class_config = new $object_class;
    
    $this->_ref_object_configs = $this->loadUniqueBackRef("object_configs");
  }
}
?>