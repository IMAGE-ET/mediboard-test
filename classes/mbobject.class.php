<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: 1793 $
 *  @author Thomas Despoix
 */

CAppUI::requireSystemClass("request");
CAppUI::requireSystemClass("mbFieldSpecFact");
CAppUI::requireSystemClass("mbObjectSpec");
 
/**
 * Class CMbObject 
 * @abstract Adds Mediboard abstraction layer functionality
 */
class CMbObject {
  static $objectCount = 0;
  static $objectCache = array();
  static $cachableCounts = array();
  static $handlers = null;
  
  /**
   * Global properties
   */
  var $_class_name    = null; // class name of the object
  var $_error         = null; // error message
  var $_id            = null; // shortcut for the object id
  var $_guid          = null; // shortcut for the object class+id
  
  var $_view          = null; // universal view of the object
  var $_shortview     = null; // universal shortview for the object
  var $_canRead       = null; // read permission for the object
  var $_canEdit       = null; // write permission for the object
  var $_external      = null; // true if object is has remote ids
  var $_locked        = null; // true if object is locked

  var $_view_template          = null; // view template path
  var $_complete_view_template = null; // complete view template path

  /**
   * Properties  specification
   */
  var $_spec          = null;    // Spécifications unifiées
  var $_helped_fields = array(); // Champs concerné par les aides a la saisie
  var $_aides         = array(); // aides à la saisie
  var $_props         = array(); // properties specifications as string
  var $_specs         = array(); // properties specifications as objects
  var $_backRefs      = array(); // Back reference specification as string
  var $_backSpecs     = array(); // Back reference specification as objects
  var $_enums         = array(); // enums fields elements
  var $_enumsTrans    = array(); // enums fields translated elements
  var $_seek          = array(); // seekable fields
  var $_nb_files_docs = null;
  
  
  /**
   * References
   */
  var $_back           = null; // Back references collections
  var $_count          = null; // Back references counts
  var $_ref_module     = null; // Parent module
  var $_ref_logs       = null; // history of the object
  var $_ref_first_log  = null;
  var $_ref_last_log   = null;
  var $_ref_last_id400 = null;
  var $_ref_notes      = array(); // Notes
  var $_ref_documents  = array(); // Documents
  var $_ref_files      = array(); // Fichiers
  var $_ref_affectations_personnel  = null;
  var $_old            = null; // Object in database
  
  // Behaviour fields
  var $_merging = null;
  
  function __construct() {
    return $this->CMbObject();
  }
  
  function CMbObject() {    
    static $spec          = null;
    static $class         = null;
    static $objectsTable  = array();
    static $props         = null;
    static $backRefs      = null;
    static $backSpecs     = array();
    static $specsObj      = null;
    static $seeks         = null;
    static $enums         = null;
    static $enumsTrans    = null;
    static $helped_fields = null;
    static $module        = null;

    // To simulate static inheritance
    static $static = false;
    
    if (!$static) {
      $class = get_class($this);
      $spec = $this->getSpec();
      $spec->init();
      
      $reflection = new ReflectionClass($class);
      $module = basename(dirname($reflection->getFileName()));
    }
    $this->_class_name =& $class;
    $this->_spec       =& $spec;
    $this->loadRefModule($module);

    if (!$static) {
      $props = $this->getSpecs();
      $this->_props =& $props;
      $backRefs = $this->getBackRefs();
      $this->_backRefs =& $backRefs;
      $specsObj = $this->getSpecsObj();
      $this->_specs =& $specsObj;
      $seeks = $this->getSeeks();
      $this->_seek =& $seeks;
      $enums = $this->getEnums();
      $this->_enums =& $enums;
      $enumsTrans = $this->getEnumsTrans();
      $this->_enumsTrans =& $enumsTrans;
      $helped_fields = $this->getHelpedFields();
      $this->_helped_fields =& $helped_fields;
      
      $static = true;
    }

    $this->_props         =& $props;
    $this->_backRefs      =& $backRefs;
    $this->_backSpecs     =& $backSpecs;
    $this->_specs         =& $specsObj;
    $this->_seek          =& $seeks;
    $this->_enums         =& $enums;
    $this->_enumsTrans    =& $enumsTrans;
    $this->_helped_fields =& $helped_fields;
    
    if ($key = $this->_spec->key) {
      $this->_id =& $this->$key;
    }
  }

  /**
   * Staticly build object handlers array
   * @return void
   */
  function makeHandlers() {
    if (CMbObject::$handlers) {
      return;
    }
    // Static initialisations
    CMbObject::$handlers = array();
    foreach (CAppUI::conf("object_handlers") as $handler => $active) {
      if ($active) {
        CMbObject::$handlers[] = new $handler;
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
  function getFormattedValue($field) {
  	return $this->_specs[$field]->getValue($this);
  }
  
  /**
   * Chargement des notes sur l'objet
   */
  function loadRefsNotes($perm = PERM_READ) {
    $this->_ref_notes = array();
    $notes = new CNote();
    if($this->_id){
      $this->_ref_notes = $notes->loadNotesForObject($this, $perm);
      return count($this->_ref_notes);
    }else {
      return 0;
    }
  }

  /**
   * Chargement des Fichiers et Documents
   */
  function loadRefsFiles() {
    $file = new CFile();
    if ($file->_ref_module && $this->_id) {
      $this->_ref_files = $file->loadFilesForObject($this);
      return count($this->_ref_files);
    }
  }

  function loadRefsDocs() {
    $document = new CCompteRendu();
    if ($document->_ref_module && $this->_id) {
      $document->object_class = $this->_class_name;
      $document->object_id    = $this->_id;
      $order = "nom";
      $this->_ref_documents = $document->loadMatchingList($order);
      return count($this->_ref_documents);
    }
  }
  
  function loadRefsFilesAndDocs() {
  	$nb_files = $this->loadRefsFiles();
    $nb_docs  = $this->loadRefsDocs();
    $this->_nb_files_docs = $nb_docs + $nb_files;
  }
  
  function getNumDocs() {
    return $this->countBackRefs("documents");
  }
  
  function getNumFiles(){
    return $this->countBackRefs("files");
  }
  
  function getNumDocsAndFiles($permType = null){
    $this->_nb_files_docs = $permType ? 
                               $this->getNumDocsAndFilesWithPerm($permType) : 
                               $this->getNumFiles() + $this->getNumDocs();
    return $this->_nb_files_docs;
  }
  
  function getNumDocsAndFilesWithPerm($permType = PERM_READ){
    $this->loadRefsFiles();
    if ($this->_ref_files) {
      foreach ($this->_ref_files as $file_id => &$file){
        if(!$file->getPerm($permType)){
          unset($this->_ref_files[$file_id]);
        }
      }
    }
    
    $this->loadRefsDocs();
    if ($this->_ref_documents) {
      foreach ($this->_ref_documents as $doc_id=>&$doc){
        if(!$doc->getPerm($permType)){
          unset($this->_ref_documents[$doc_id]);
        }
      }
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
    if ($value !== null && $this->$field !== $value) {
      return false;
    }
    
    // Has it finally been modified ?
    return $this->$field != $this->_old->$field;
  }
  
  /**
   * Complete field with base value if missing
   * @param field string Field name
   */
  function completeField($field) {
    // Field is valued or Nothing in base
    if ($this->$field !== null || !$this->_id) {
      return;
    }
    
    $this->loadOldObject();
    $this->$field = $this->_old->$field;
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
    return(CPermObject::getPermObject($this, $permType));
  }
  
  function canRead() {
    return $this->_canRead = $this->getPerm(PERM_READ);
  }
  
  function canEdit() {
    return $this->_canEdit = $this->getPerm(PERM_EDIT);;
  }

  function canDo(){
    $canDo = new CCanDo;
    $canDo->read  = $this->canRead();
    $canDo->edit  = $this->canEdit();
    return $this->_can = $canDo;
  }
  
  function loadListWithPerms($permType = PERM_READ, $where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    $list = $this->loadList($where, $order, $limit, $group, $leftjoin);
    
    // Filter with permission
    if ($permType) {
      foreach ($list as $key=>$element){
        if(!$element->getPerm($permType)){
          unset($list[$key]);
        }
      }
    }

    return $list;
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
   * Object(s) Loaders
   */

  // One object by ID
  function load($oid = null, $strip = true) {
  	if ($oid) {
      $this->_id = $oid;
    }

    if (!$this->_id || !$this->_spec->table || !$this->_spec->key) {
      return false;
    }
    
    $sql = "SELECT * FROM `{$this->_spec->table}` WHERE `{$this->_spec->key}` = '$this->_id'";
    
    $object = $this->_spec->ds->loadObject($sql, $this);
      
    if (!$object) {
      $this->_id = null;
      return false;
    }

    $this->registerCache();
    $this->checkConfidential();
    $this->updateFormFields();
        
    return $this;
  }
  
  /**
   * Register the object into cache
   * @return void
   */
  function registerCache() {
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
   * Retrieve an already rigistered object from cache if available,
   * performs a standard load otherwise
   *
   * @param ref $id The actual object identifier
   * @return the retrieved object
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
   * @param string $order Order SQL statement
   * @param string $group Group by SQL statement
   * @param array leftjoin Left join SQL statement collection
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
   * Loads the list which objects match defined properties
   * @param string $order Order SQL statement
   * @param string $limit Limit SQL statement
   * @param string $group Group by SQL statement
   * @param array leftjoin Left join SQL statement collection
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
   * Object count of the list which objects match defined properties
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
   * @param $where array Array of where clauses
   * @param $order array Array of order fields
   * @param $limit string MySQL limit clause
   * @param $group array Array of group by clauses
   * @param $leftjoin array Array of left join clauses
   * @return array[CMbObject] List of found objects, null if module is not installed
   */
  function loadList($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
  	$request = new CRequest();
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);
    
    $result = $this->loadQueryList($request->getRequest($this));
    
    return $result;
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
    
    $result = $this->_spec->ds->exec($request->getCountRequest($this));
    $row = $this->_spec->ds->fetchArray($result);
    
    return $row["total"];
  }
  
  function loadListByReq($request) {
  	return $this->loadQueryList($request->getRequest($this));
  }
  
  /**
   * return an array of objects from a SQL SELECT query
   * class must implement the Load() factory, see examples in Webo classes
   * @note to optimize request, only select object oids in $sql
   */
  function loadQueryList($sql) {
    $cur = $this->_spec->ds->exec($sql);
    $list = array();
    while ($row = $this->_spec->ds->fetchAssoc($cur)) {
      $newObject = new $this->_class_name;
      $newObject->bind($row, false);
      $newObject->checkConfidential();
      $newObject->updateFormFields();
      $newObject->registerCache();
      $list[$newObject->_id] = $newObject;
    }

    $this->_spec->ds->freeResult($cur);
    return $list;
  }

  /**
   *  Clone the current record
   *  @return object  The new record object or null if error
   */

  function cloneObject() {
    $_key = $this->_spec->key;
    
    $newObj = $this;
    // blanking the primary key to ensure that's a new record
    $newObj->$_key = "";
    
    return $newObj;
  }
  
  /**
   * Update the form fields from the DB fields
   */

  function updateFormFields() {
    $this->_guid = "$this->_class_name-$this->_id";
    $this->_view = "$this->_class_name#$this->_id";
    $this->_shortview = "#$this->_id";
    if ($module = $this->_ref_module) {
      $path = "$module->mod_name/templates/$this->_class_name";
	    $this->_view_template          = "{$path}_view.tpl";
	    $this->_complete_view_template = "{$path}_complete.tpl";
    }
  }
  
  /**
   * Load object view information 
   */
  
  function loadView() {
    $this->loadRefsFwd();
  }
  
  /**
   * Load complete object view information 
   */
  
  function loadComplete() {
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
  function extendsWith($mbObject) {
    $targetClass = get_class($mbObject);
    $thisClass = get_class($this);
    if ($targetClass != $thisClass) {
      trigger_error(printf("Target object has not the same class (%s) as this (%s)", $targetClass, $thisClass), E_USER_WARNING);
      return;
    }
    
    foreach ($mbObject->getDBFields() as $propName => $propValue) {
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
    foreach ($this->getProps() as $name => $value) {
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
    
    $msg = null;
    $properties = get_object_vars($this);
    
    foreach($this->_props as $propName => $propSpec) {
      if ($propName[0] != '_') {
        if (!array_key_exists($propName, $properties)) {
          trigger_error("La spécification cible la propriété '$propName' inexistante dans la classe '$this->_class_name'", E_USER_WARNING);
        } else {
          $propValue =& $this->$propName;
          if(($propValue !== null) || (!$this->_id)) {
            $msgProp = $this->checkProperty($propName);
          
            $debugInfo = $debug ? "(val:'$propValue', spec:'$propSpec')" : "";
            $msg .= $msgProp ? "<br/> => $propName : $msgProp $debugInfo" : null;
          }
        }
      }
    }
    
    return $msg;
  }

  /**
   * Update the form fields from the DB fields
   */

  function updateDBFields() {
  }
  
  
  function log() {
  	global $AppUI;
  	
    // Si object non loggable
    if (!$this->_spec->loggable){
      return;
    }

    // Analyse changes fields
    $fields = array();
    $db_fields = $this->getDBFields();
    foreach ($db_fields as $propName => $propValue) {
      if ($this->fieldModified($propName)) {
        $fields[] = $propName;
      }
    }
    
    $object_id = $this->_id;
    
    $type = "store";
    if ($this->_old->_id == null) {
      $type = "create";
      $fields = array();
    }
    
    if ($this->_id == null) {
      $type = "delete";
      $object_id = $this->_old->_id;
      $fields = array();
    }

    if (!count($fields) && $type == "store") {
      return;
    }
    
    $system_version = explode(".", CModule::getInstalled("system")->mod_version);
    if ($system_version[0] == 1 && $system_version[1] == 0 && $system_version[2] < 4){
      return;	
    }
    
    $log = new CUserLog;
    $log->user_id = $AppUI->user_id;
    $log->object_id = $object_id;
    $log->object_class = $this->_class_name;
    $log->type = $type;
    $log->_fields = $fields;
    $log->date = mbDateTime();
    $log->store();
    
    $this->_ref_last_log = $log;
  }
  
  
  
  /**
   * Inserts a new row if id is zero or updates an existing row in the database table
   * @param boolean $checkobject check values before storing if true (default)
   * @return null|string null if successful otherwise returns and error message
   */
  function store() {
    // Properties checking
    $this->updateDBFields();

    $this->loadOldObject();
    
    if ($msg = $this->check()) {
      return CAppUI::tr(get_class($this)) . 
        CAppUI::tr("CMbObject-msg-check-failed") .
        CAppUI::tr($msg);
    }

    // DB query
    if ($this->_old->_id) {
      $ret = $this->_spec->ds->updateObject($this->_spec->table, $this, $this->_spec->key, $this->_spec->nullifyEmptyStrings);
    } else {
      $keyToUpadate = $this->_spec->incremented ? $this->_spec->key : null;
      $ret = $this->_spec->ds->insertObject($this->_spec->table, $this, $keyToUpadate);
    }
    

    if (!$ret) {
        return get_class($this)."::store failed <br />" . $this->_spec->ds->error();
    } 
    

    // Load the object to get all properties
    $this->load();
    
    // Creation du log une fois le store terminé
    $this->log();
    
    // Trigger event
    $this->onStore();

    $this->_old = null;
    return null;
  }

  /**
   * Trigger store event for handlers
   * @return void
   */
  function onStore() {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onStore($this);
    }
  }
  
  /**
   * Trigger merge event for handlers
   * @return void
   */
  function onMerge() {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onMerge($this);
    }
  }
  
  /**
   * Count number back refreferecing object
   * @param string $backName name the of the back references to count
   * @return int the count null if back references module is not installed
   */
  function countBackRefs($backName) {
    $backRef = $this->_backRefs[$backName];
    $backRefParts = split(" ", $backRef);
    $backClass = $backRefParts[0];
    $backField = $backRefParts[1];
    $backObject = new $backClass;

    // Cas du module non installé
    if (!$backObject->_ref_module) {
      return;
    }

    // Empty object
    if (!$this->_id || !$backObject->_spec->table || !$backObject->_spec->key) {
      return 0;
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
  function loadBackRefs($backName, $order = null, $limit = null) {
    $this->makeBackSpec($backName);
        
    // Spécifications
    $backSpec = $this->_backSpecs[$backName];
    $backObject = new $backSpec->class;
    $backField = $backSpec->field;
    $fwdSpec =& $backObject->_specs[$backField];
    $backMeta = $fwdSpec->meta;      

    // Cas du module non installé
    if (!$backObject->_ref_module) {
      return;
    }
    
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
    
    return $this->_back[$backName] = $backObject->loadMatchingList($order, $limit);
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
    foreach ($this->_backRefs as $backName => $backRef) {
      $this->loadBackRefs($backName, null, $limit);
      $this->countBackRefs($backName);
    }
  }

  /**
   * Transfer all back refs from given object of same class
   * @param CMbObject $object
   * @return string store-like error message if failed, null if successful
   */
  function transferBackRefsFrom(CMbObject &$object) {
    if (!$object->_id) {
      return "transferNoId";
    }
    if ($object->_class_name != $this->_class_name) {
      return "transferDifferentType";
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
   * Check whether the object can be deleted.
   * Default behaviour counts for back reference without cascade 
   * @return null if ok error message otherwise
   */
  function canDeleteEx() {
    // Empty object
    if (!$this->_id) {
      return CAppUI::tr("noObjectToDelete") . " " . CAppUI::tr($this->_class_name);
    }
    
    // Counting backrefs
    $issues = array();
    foreach ($this->_backRefs as $backName => $backRef) {
      $backRefParts = split(" ", $backRef);
      $backClass = $backRefParts[0];
      $backField = $backRefParts[1];
      $backObject = new $backClass;
      $backSpec =& $backObject->_specs[$backField];
      $backMeta = $backSpec->meta;      

      // Cas du module non installé
      if (!$backObject->_ref_module) {
        continue;
      }
      
      // Cas de la suppression en cascade
      if ($backSpec->cascade) {
        
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
            . " " . CAppUI::tr("$backSpec->class-back-$backName");
        }
        
        continue;
      }
      
      // Vérification du nombre de backRefs
      if (!$backSpec->unlink) {
        if ($backCount = $this->countBackRefs($backName)) {
          $issues[] = $backCount 
            . " " . CAppUI::tr("$backSpec->class-back-$backName");
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
   * @return null|string null if successful otherwise returns and error message
   */
  function delete() {
    $this->loadOldObject();
    
    if ($msg = $this->canDeleteEx()) {
      return $msg;
    }

    // Deleting backRefs
    foreach ($this->_backRefs as $backName => $backRef) {
      $backRefParts = explode(' ', $backRef);
      $backClass = $backRefParts[0];
      $backField = $backRefParts[1];
      $backObject = new $backClass;
      $backSpec =& $backObject->_specs[$backField];
      $backMeta = $backSpec->meta;      
      
      /* Cas du module non installé, 
       * Cas de l'interdiction de suppression, 
       * Cas de l'interdiction de la non liaison des backRefs */
      if (!$backObject->_ref_module || !$backSpec->cascade|| $backSpec->unlink) {
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
   
    // Creation du log une fois le delete terminé
    $this->log();
    
    // Event Handlers
    $this->onDelete();

    $this->_old = null;
    return null;
  }

  /**
   * Trigger delete event for handlers
   * @return void
   */
  function onDelete() {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onDelete($this);
    }
  }
  

  /**
   *  Generic seek method
   *  @return the first 100 records which fits the keywords
   */
  function seek($keywords) {
    $sql = "SELECT * FROM `{$this->_spec->table}` WHERE 1";
    if(count($keywords) and count($this->_seek)) {
      foreach($keywords as $key) {
        $sql .= "\nAND (0";
        foreach($this->_seek as $keySeek => $spec) {
          $listSpec = explode('|', $spec);
          switch($listSpec[0]) {
            case "equal":
              $sql .= "\nOR `$keySeek` = '$key'";
              break;
            case "like":
              $sql .= "\nOR `$keySeek` LIKE '%$key%'";
              break;
            case "likeBegin":
              $sql .= "\nOR `$keySeek` LIKE '$key%'";
              break;
            case "likeEnd":
              $sql .= "\nOR `$keySeek` LIKE '%$key'";
              break;
            case "ref":
              $refObj = new $listSpec[1];
              $refObj = $refObj->seek($keywords);
              if(count($refObj)) {
                $listIds = implode(",", array_keys($refObj));
                $sql .= "\nOR `$keySeek` IN ($listIds)";
              }
              break;
          }
        }
        $sql .= "\n)";
      }
    } else {
      $sql .= "\nAND 0";
    }
    $sql .= "\nORDER BY";
    foreach($this->_seek as $keySeek => $spec) {
      $sql .= "\n$keySeek,";
    }
    $sql .= "\n {$this->_spec->key}";
    $sql .=" LIMIT 0,100";
    
    return $this->loadQueryList($sql);
  }
  
  /**
   * Get DB fields and there values
   * @return array Associative array
   */
  function getDBFields() {
    $result = array();
    $vars = get_object_vars($this);
    foreach($vars as $key => $value) {
      if ($key[0] != '_') {
        $result[$key] = $value;
      }
    }

    return $result;
  }
  
  /**
   * Escape DB Fields for SQL queries
   * @return void
   */
  function escapeDBFields() {
  	$db_fields = $this->getDBFields();
    foreach ($db_fields as $propName => $propValue) {
      if ($propValue) {
        $this->$propName = addslashes($propValue);
      }
    }
  }
  
  /**
   * Unescape DB Fields for SQL queries
   * @return void
   */
  function unescapeDBFields() {
  	$db_fields = $this->getDBFields();
    foreach ($db_fields as $propName => $propValue) {
      if ($propValue) {
        $this->$propName = stripslashes($propValue);
      }
    }
  }
  
  /**
   * Get object properties, i.e. having specs
   * @return array Associative array
   */
  function getProps() {
    $result = array();
    
    foreach ($this->_specs as $key => $value) {
      $result[$key] = $this->$key;
    }

    return $result;
  }
  
  /**
   * Get properties specifications
   * @return array
   */
  function getSeeks() {
    return array();
  }

  /**
   * Get seek specifications
   * @return array
   */
  function getSpecs() {
    return array (
      "_shortview" => "str",
      "_view" => "str",
      $this->_spec->key => "ref class|$this->_class_name"
    );
  }
  
  /**
   * Get backward reference specifications
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackRefs() {
    return array (
      "identifiants"           => "CIdSante400 object_id",
      "notes"                  => "CNote object_id",
      "files"                  => "CFile object_id",
      "documents"              => "CCompteRendu object_id",
      "permissions"            => "CPermObject object_id",
      "logs"                   => "CUserLog object_id",
      "affectations_personnel" => "CAffectationPersonnel object_id",
    );
  }
  
  /**
   * Liste des champs d'aides à la saisie
   * @return array
   */
  function getHelpedFields() {
    return array();
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
      return;
    }
    return $this->_backSpecs[$backName] = new CMbBackSpec($backName, $this->_backRefs[$backName]);
  }

  /**
   * Converts string specifications to objet specifications
   * @TODO Ref: should parse props instead of object_vars 
   */
  function getSpecsObjNew() {
    $specs =& $this->_props;
    $props = get_object_vars($this);
    
    $spec = array();
    foreach ($props as $k => $v) {
      $spec[$k] = CMbFieldSpecFact::getSpec($this, $k, array_key_exists($k, $specs) ? $specs[$k] : null);
    }
    return $spec;
  }
  
  /**
   * Converts string specifications to objet specifications
   * Optimized version
   */
    function getSpecsObj() {
    $spec = array();
    foreach ($this->_props as $propName => $propSpec) {
      $spec[$propName] = CMbFieldSpecFact::getSpec($this, $propName, $propSpec);
    }
    return $spec;
  }
  
  /**
   * Build Enums variant returning values
   */  
  function getEnums() {
    $enums = array();
    foreach ($this->_specs as $propName => $spec) {
      if ($spec instanceof CEnumSpec) {
      	$spec->_list = $enums[$propName] = explode("|", $spec->list);
      }
      if ($spec instanceof CBoolSpec) {
        $enums[$propName] = array(0,1);
      }
    }
    
    return $enums;
  }
  
  /**
   * Build Enums variant returning values
   */
  function getEnumsTrans() {
    $enumsTrans = array();
    foreach ($this->_enums as $propName => $enumValues) {
      $enumsTrans[$propName] = array_flip($enumValues);
      foreach($enumsTrans[$propName] as $key => $item) {
        $enumsTrans[$propName][$key] = CAppUI::tr("$this->_class_name.$propName.$key");
      }
      asort($enumsTrans[$propName]);
    }
        
    return $enumsTrans;
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
    $ds =& $this->_spec->ds;
    return $ds->loadTable($this->_spec->table);    
  }

  /**
   * Charge toutes les aides à la saisie de l'objet pour un utilisateur donné
   *
   * @param ref|CUser $user_id ID de l'utilisateur
   * @param string $needle Permet de filtrer les aides commançant par le filtre, si non null
   */
  function loadAides($user_id, $needle = null) {
    // Initialisation des aides
  	foreach ($this->_helped_fields as $field => $prop) {
      $this->_aides[$field] = array("no_enum" => null);
      if ($prop) {
      	// Création des entrées pour les enums
        $this->_aides[$field][''] = null;
        foreach($this->_enums[$prop] as $valueEnum) {
          $this->_aides[$field][$valueEnum] = null;
        }
      }
    }
    
    // Chargement de l'utilisateur courant
    $user = new CMediusers();
    $user->load($user_id);
    
    // Préparation du chargement des aides
    $ds =& $this->_spec->ds;
    $where = array();
    
    $where["user_id"] = $ds->prepare("= %", $user_id);
    $where["class"]   = $ds->prepare("= %", $this->_class_name);
    
    if ($needle) {
      $where[] = $ds->prepare("name LIKE %1 OR text LIKE %2", "%$needle%","%$needle%");
    }
    
    $order = "name";
    
    // Chargement des Aides de l'utilisateur
    $aide = new CAideSaisie();
    $aides = $aide->loadList($where,$order); 
    $this->orderAides($aides, "Aides du praticien");

    // Chargement des Aides de la fonction de l'utilisateur
    unset($where["user_id"]);
    $where["function_id"] = $ds->prepare("= %", $user->function_id);
    $aides = $aide->loadList($where, $order);  
    $this->orderAides($aides, "Aides du cabinet");
  }
  
  function orderAides($aides, $title) {
    foreach ($aides as $aide) {
      $curr_aide =& $this->_aides[$aide->field];
      
      // Ajout de l'aide à la liste générale
      $curr_aide["no_enum"][$title][$aide->text] = $aide->name; 
      
      // Verification de l'existance des clé dans le tableaux
      $linkField = @$this->_helped_fields[$aide->field];
      if (!$aide->depend_value && $linkField) {
        // depend de toutes les entrées
        foreach ($this->_enums[$linkField] as $valueEnum){
          $curr_aide[$valueEnum][$title][$aide->text] = $aide->name;
        }
        
        $curr_aide[""][$title][$aide->text] = $aide->name;
      }
      
      if ($aide->depend_value) {
        $curr_aide[$aide->depend_value][$title][$aide->text] = $aide->name;
      }
    }
  }
  
  function loadLogs() {
    $order = "date ASC";
    $limit = "0, 100";
    $this->_ref_logs = $this->loadBackRefs("logs", $order, $limit);

    foreach($this->_ref_logs as &$_log) {
      $_log->loadRefsFwd();
    }
     
    $this->_ref_first_log = reset($this->_ref_logs);
    $this->_ref_last_log  = end($this->_ref_logs);
  }

  
  function loadLastLogForField($fieldName){	
  	$log = new CUserLog();
  	$where = array();
  	$order = "date DESC";
  	$where["object_id"] = " = '$this->_id'";
  	$where["object_class"] = " = '$this->_class_name'";
  	$where["fields"] = " LIKE '%$fieldName%'";
  	$log->loadObject($where, $order);
  	if($log->_id){
  	  $log->loadRefsFwd();
  	}
  	return $log;
  }
  
  function loadAffectationsPersonnel() {
    if (null == $affectations = $this->loadBackRefs("affectations_personnel")) {
      return;
    }
    // Initialisation
    $this->_ref_affectations_personnel["op"] = array();
    $this->_ref_affectations_personnel["op_panseuse"] = array();
    $this->_ref_affectations_personnel["reveil"] = array();
    $this->_ref_affectations_personnel["service"] = array();
    
    foreach($affectations as $key => $affectation){
      $affectation->loadRefPersonnel();
      $affectation->_ref_personnel->loadRefUser();
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
}
?>