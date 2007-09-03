<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: 1793 $
 *  @author Thomas Despoix
 */

require_once("./classes/request.class.php");
require_once("./classes/mbFieldSpecFact.class.php");
require_once("./classes/mbObjectSpec.class.php");
 
/**
 * Class CMbObject 
 * @abstract Adds Mediboard abstraction layer functionality
 */
class CMbObject {
  static $objectCount = 0;
  static $cacheCount = 0;
  static $handlers = null;
  
  /**
   * Global properties
   */
  
  var $_objectsTable = null; // list of loaded objects
  
  var $_class_name    = null; // class name of the object
  var $_tbl           = null; // table name
  var $_tbl_key       = null; // primary key name
  var $_error         = null; // error message
  var $_id            = null; // universal shortcut for the object id
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
  
  var $_back          = null; // All back references
  var $_ref_module    = null; // Parent module
  var $_ref_logs      = null; // history of the object
  var $_ref_first_log = null;
  var $_ref_last_log  = null;
  var $_ref_notes     = array(); // Notes
  var $_ref_documents = array(); // Documents
  var $_ref_files     = array(); // Fichiers

  /**
   * Constructor
   */
 
  function CMbObject($table, $key) {
    self::$objectCount++;
    
    $this->_tbl     = $table;
    $this->_tbl_key = $key;
    $this->_id      =& $this->$key;
    
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

    static $static = false;
    if (!$static) {
      $spec = $this->getSpec();
      
      //initialisation de la connexion
      $spec->ds = CSQLDataSource::get("std");
      
      $class = get_class($this);
      $this->_class_name =& $class;
      $this->_objectsTable =& $objectsTable;
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
    
    $this->_spec          =& $spec;
    $this->_class_name    =& $class;
    $this->_objectsTable  =& $objectsTable;
    $this->_props         =& $props;
    $this->_backRefs      =& $backRefs;
    $this->_backSpecs     =& $backSpecs;
    $this->_specs         =& $specsObj;
    $this->_seek          =& $seeks;
    $this->_enums         =& $enums;
    $this->_enumsTrans    =& $enumsTrans;
    $this->_helped_fields =& $helped_fields;    
  }

  /**
   * Saticly build object handlers array
   */
  function makeHandlers() {
    if (CMbObject::$handlers) {
      return;
    }
    // Static initialisations
    global $dPconfig;
    CMbObject::$handlers = array();
    foreach ($dPconfig["object_handlers"] as $handler => $active) {
      if ($active) {
        CMbObject::$handlers[] = new $handler;
      }
    }
  }

  /**
   * Initilize object specification
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    return new CMbObjectSpec;
  }
  /**
   * Set/get functions
   */
  function getError() {
    return $this->_error;
  }
  
  /**
   * Chargement des notes sur l'objet
   */
  function loadRefsNotes($perm = PERM_READ) {
    $this->_ref_notes = new CNote();
    if($this->_id){
      $this->_ref_notes = $this->_ref_notes->loadNotesForObject($this, $perm);
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

    return;
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
    
    return;
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
    if(!$permType){
      $this->_nb_files_docs = $this->getNumFiles() + $this->getNumDocs();
    }else{
      $this->_nb_files_docs = $this->getNumDocsAndFilesWithPerm($permType);
    }
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
   * Permission generic check
   * return true or false
   */

  function getPerm($permType) {
    return(CPermObject::getPermObject($this, $permType));
  }
  
  function canRead() {
    $this->_canRead = $this->getPerm(PERM_READ);
    return $this->_canRead;
  }
  
  function canEdit() {
    $this->_canEdit = $this->getPerm(PERM_EDIT);
    return $this->_canEdit;
  }

  function canDo(){
    $canDo = new CCanDo;
    $canDo->read  = $this->canRead();
    $canDo->edit  = $this->canEdit();
    
    return $canDo;
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
   */

  function bind($hash, $doStripSlashes = true) {
    bindHashToObject($hash, $this, $doStripSlashes);
    return true;
  }
  
  /**
   * Object(s) Loaders
   */

  // One object by ID
  function load($oid = null, $strip = true) {
    global $dPconfig;
    
  	if ($oid) {
      $this->_id = intval($oid);
    }

    if (!$this->_id) {
      return false;
    }
    
    $sql = "SELECT * FROM `$this->_tbl` WHERE `$this->_tbl_key` = '$this->_id'";
    
    $object = $this->_spec->ds->loadObject($sql, $this);
      
    if (!$object) {
      $this->_id = null;
      return false;
    }

    //$this->_objectsTable[$oid] = $this;
    $this->checkConfidential();
    $this->updateFormFields();
    return $this;
  }
  
  /**
   * Loads the first object matching defined properties
   */
  function loadMatchingObject($order = null, $group = null, $leftjoin = null) {
    $request = new CRequest;
    $request->addLJoin($leftjoin);
    $request->addGroup($group);
    $request->addOrder($order);

    $this->updateDBFields();
    foreach($this->getProps() as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    
    $this->loadObject($request->where, $request->order, $request->group, $request->ljoin);
  
  
  }
  
  /**
   * Loads the list which objects match defined properties
   */
  function loadMatchingList($order = null, $limit = null, $group = null, $leftjoin = null) {
    $request = new CRequest;
    $request->addLJoin($leftjoin);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $this->updateDBFields();
    foreach($this->getProps() as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    return $this->loadList($request->where, $request->order, $request->limit, $request->group, $request->ljoin);
  }
  
  /**
   * Loads the first object matching the query
   */
  function loadObject($where = null, $order = null, $group = null, $leftjoin = null) {
    $request = new CRequest;
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit("0,1");
    $list =& $this->loadList($request->where, $request->order, $request->limit, $request->group, $request->ljoin);

    foreach ($list as $object) {
      foreach($object->getProps() as $key => $value) {
        $this->$key = $value;
      }
      $this->updateFormFields();
      return true;
    }
    return false;
  }
  
  /**
   * Object list by a request constructor
   */
  function loadList($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    global $dPconfig;
    
  	$request = new CRequest();
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);
    
    $result = $this->loadQueryList($request->getRequest($this));
    
    return $result;
  }
  
  function loadListByReq($request) {
    global $dPconfig;

    $result = $this->loadQueryList($request->getRequest($this));
  	return $result;
  }
  
  /**
   * return an array of objects from a SQL SELECT query
   * class must implement the Load() factory, see examples in Webo classes
   * @note to optimize request, only select object oids in $sql
   */
  function loadQueryList($sql, $maxrows = null) {
    $cur = $this->_spec->ds->exec($sql);
    $list = array();
    $cnt = 0;
    $class = get_class($this);
    $table_key = $this->_tbl_key;
    while ($row = $this->_spec->ds->fetchArray($cur)) {
      $key = $row[$table_key];
      $newObject = new $class();
      $newObject->bind($row, false);
      $newObject->checkConfidential();
      $newObject->updateFormFields();
      $list[$newObject->_id] = $newObject;
      if($maxrows && $maxrows == $cnt++) {
        break;
      }
    }
    $this->_spec->ds->freeResult($cur);
    return $list;
  }

  /**
   *  Clone the current record
   *  @return object  The new record object or null if error
   */

  function cloneObject() {
    $_key = $this->_tbl_key;
    
    $newObj = $this;
    // blanking the primary key to ensure that's a new record
    $newObj->$_key = "";
    
    return $newObj;
  }
  
  /**
   * Update the form fields from the DB fields
   */

  function updateFormFields() {
    $k                    = $this->_tbl_key;
    $this->_view          = $this->_tbl . " #" . $this->$k;
    $this->_shortview     = "#" . $this->$k;
    $this->_view_template     = "{$this->_ref_module->mod_name}/templates/{$this->_class_name}_view.tpl";
    $this->_complete_view_template = "{$this->_ref_module->mod_name}/templates/{$this->_class_name}_complete.tpl";
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
   * Nullify object properties that evaluate to false
   */
  function nullifyEmptyFields() {
    foreach ($this->getProps() as $propName => $propValue) {
      if (!$propValue) {
        $this->$propName = null;
      }
    }
  }
  
  /**
   * Extends object properties with target object (of the same class) properties
   */
  function extendsWith($mbObject) {
    $targetClass = get_class($mbObject);
    $thisClass = get_class($this);
    if ($targetClass != $thisClass) {
      trigger_error(printf("Target object has not the same class (%s) as this (%s)", $targetClass, $thisClass), E_USER_WARNING);
      return;
    }
    
    foreach ($mbObject->getProps() as $propName => $propValue) {
      if ($propValue !== null) {
        $this->$propName = $propValue;
      }
    }
  }
  
  function loadRefModule($name) {
    $this->_ref_module = CModule::getActive($name);
  }

  /**
   *  Generic check method
   *  @return null if the object is ok a message if not
   */

  function repair() {
    global $dPconfig;
    $properties = get_object_vars($this);
    foreach($this->_props as $propName => $propSpec) {
      if (!array_key_exists($propName, $properties)) {
        trigger_error("La spécification cible la propriété '$propName' inexistante dans la classe '$this->_class_name'", E_USER_WARNING);
        continue;
      } 

      $propValue =& $this->$propName;
      if ($propValue !== null) {
        if ($msg = $this->checkProperty($propName)) {
          if (!$this->lookupSpec("notNull", $propSpec)) {
            $propValue = "";
          }
        }
      }
    }
  }

  /**
   *  Generic check method
   *  @return null if the object is ok a message if not
   */

  function check() {
    global $dPconfig;
    $msg = null;
    $properties = get_object_vars($this);
    
    foreach($this->_props as $propName => $propSpec) {
      if(!array_key_exists($propName, $properties)) {
        trigger_error("La spécification cible la propriété '$propName' inexistante dans la classe '$this->_class_name'", E_USER_WARNING);
      } else {
        $propValue =& $this->$propName;
        if(($propValue !== null) || (!$this->_id)) {
          $msgProp = $this->checkProperty($propName);
          
          $debugInfo = $dPconfig["debug"] ? "(val:'$propValue', spec:'$propSpec')" : "";
          $msg .= $msgProp ? "<br/> => $propName : $msgProp $debugInfo" : null;
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
  
  
  function log($objBefore) {
  	global $AppUI;

    // Si object non loggable
    if (!$this->_spec->loggable){
      return;
    }

    // Analyse changes fields
    $fields = array();
    foreach ($this->getProps() as $propName => $propValue) {
    	if ($propValue !== null) {
        $propValueBefore = $objBefore->$propName;
        if ($propValueBefore != $propValue) {
          $fields[] = $propName;
        }
      }
    }
    
    $object_id = $this->_id;
    
    $type = "store";
    if ($objBefore->_id == null) {
      $type = "create";
      $fields = array();
    }
    
    if ($this->_id == null) {
      $type = "delete";
      $object_id = $objBefore->_id;
      $fields = array();
    }

    if (!count($fields) && $type == "store") {
      return;
    }
    
    if(CModule::getInstalled("system")->mod_version < "1.0.4"){
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
  }
  
  
  
  /**
   *  Inserts a new row if id is zero or updates an existing row in the database table
   *  @param boolean $checkobject check values before storing if true (default)
   *  @return null|string null if successful otherwise returns and error message
   */
  function store($checkobject = true) {
    global $dPconfig;
  	global $AppUI;
    
    // Properties checking
    $this->updateDBFields();

    // Si l'object existe alors, on affecte à objBefore sa valeur
    $objBefore = new $this->_class_name();
    $objBefore->load($this->_id);
    
    if ($checkobject) {
      if ($msg = $this->check()) {
        return $AppUI->_(get_class($this)) . 
          $AppUI->_("::store-check failed:") .
          $AppUI->_($msg);
      }
    }

    // DB query
    if ($objBefore->_id) {
        $ret = $this->_spec->ds->updateObject($this->_tbl, $this, $this->_tbl_key);
    } else {
        $ret = $this->_spec->ds->insertObject($this->_tbl, $this, $this->_tbl_key);
    }
    

    if (!$ret) {
        return get_class($this)."::store failed <br />" . $this->_spec->ds->error();
    } 
    

    // Load the object to get all properties
    unset($this->_objectsTable[$this->_id]);
    $this->load();
    
    //Creation du log une fois le store terminé
    $this->log($objBefore);
    
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onStore($this);
    }

    return null;
  }

  
  /**
   * Count number back refreferecing object
   * @param string $backName name the of the back references to count
   * @return int the count null if back references module is not installed
   */
  function countBackRefs($backName) {
  	global $dPconfig;
    
    $backRef = $this->_backRefs[$backName];
    $backRefParts = split(" ", $backRef);
    $backClass = $backRefParts[0];
    $backField = $backRefParts[1];
    $backObject = new $backClass;

    // Cas du module non installé
    if (!$backObject->_ref_module) {
      return;
    }
    
    $query = "SELECT COUNT($backObject->_tbl_key) " .
      "\nFROM `$backObject->_tbl` " .
      "\nWHERE `$backField` = '$this->_id'";

    // Cas des meta objects
    $backSpec =& $backObject->_specs[$backField];
    $backMeta = $backSpec->meta;      
    if ($backMeta) {
      $query .= "\nAND `$backMeta` = '$this->_class_name'";
    }
    
    // Comptage des backrefs
    return $this->_spec->ds->loadResult($query);	
    
  }

  /**
   * Load named back references
   * @return null
   */
  function loadBackRefs($backName = null, $order = null, $limit = null) {
    // Empty object
    if (!$this->_id) {
      return;
    }
    
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
    
    // Vérification de la possibilité de supprimer chaque backref
    $backObject->$backField = $this->_id;

    // Cas des meta objects
    if ($backMeta) {
      $backObject->$backMeta = $this->_class_name;
    }
    
    return $this->_back[$backSpec->name] = $backObject->loadMatchingList($order, $limit);
  }
  
  /**
   * Load all back references
   * @return null
   */
  function loadAllBackRefs() {
    foreach ($this->_backRefs as $backName => $backRef) {
      $this->loadBackRefs($backName);
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
      return "trasferDifferentType";
    }
    
    $object->loadAllBackRefs();
    foreach ($object->_back as $backName => $backObjects) {
      if (count($backObjects)) {
        $backSpec = $this->_backSpecs[$backName];
        $backObject = new $backSpec->class;
		    $backField = $backSpec->field;
		    $fwdSpec =& $backObject->_specs[$backField];
	      
        // Change back field and store back objects
	      foreach ($backObjects as $backObject) {
          $backObject->$backField = $this->_id;
          if ($msg = $backObject->store()) {
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
    global $AppUI;

    // Empty object
    if (!$this->_id) {
      return $AppUI->_("noObjectToDelete") . " " . $AppUI->_($this->_class_name);
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
          $issues[] = $AppUI->_("cascadeIssues")
            . " " . $cascadeIssuesCount 
            . "/" . count($cascadeObjects) 
            . " " . $AppUI->_("$backSpec->class-back-$backName");
        }
        
        continue;
      }
      
      // Vérification du nombre de backRefs
      if (!$backSpec->unlink) {
        if ($backCount = $this->countBackRefs($backName)) {
          $issues[] = $backCount 
            . " " . $AppUI->_("$backSpec->class-back-$backName");
        }
      }
    };
    $msg = count($issues) ?
      $AppUI->_("noDeleteRecord") . ": " . implode(", ", $issues) :
      null;
    
    return $msg;
  }

  /**
   * Default delete method
   * @return null|string null if successful otherwise returns and error message
   */
  function delete($oid = null) {
    global $dPconfig;
    
    // Chargement de _objBefore 
    $objBefore = new $this->_class_name;
    $objBefore->load($this->_id);
    
    
    if ($oid) {
      $this->_id = intval($oid);
    }
    
    if ($msg = $this->canDeleteEx()) {
      return $msg;
    }

    // Deleting backRefs
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
      
      // Cas de l'interdiction de suppression
      if (!$backSpec->cascade) {
        continue;
      }
      
      // Cas de l'interdiction de la non liaison des backRefs
      if ($backSpec->unlink) {
        continue; 
      }
      
      $backObject->$backField = $this->_id;
      
      // Cas des meta objects
      if ($backMeta) {
        $backObject->$backMeta = $this->_class_name;
      }

      foreach ($backObject->loadMatchingList() as $object) {
        $object->delete();
      }
    }
    
    // Actually delete record
    $sql = "DELETE FROM $this->_tbl WHERE $this->_tbl_key = '$this->_id'";
    		 
    if (!$this->_spec->ds->exec($sql)) {
      return $this->_spec->ds->error();
    }
   
    // Deletion successful
    $this->_id = null;
   
    // Creation du log une fois le delete terminé
    $this->log($objBefore);
    
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      $handler->onDelete($this);
    }   

    return null;
  }
  

  /**
   *  Generic seek method
   *  @return the first 100 records which fits the keywords
   */
  function seek($keywords) {
    $sql = "SELECT * FROM `$this->_tbl` WHERE 1";
    if(count($keywords) and count($this->_seek)) {
      foreach($keywords as $key) {
        $sql .= "\nAND (0";
        foreach($this->_seek as $keySeek => $spec) {
          $listSpec = array();
          $listSpec = explode("|", $spec);
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
    $sql .= "\n $this->_tbl_key";
    $sql .=" LIMIT 0,100";
    
    return $this->loadQueryList($sql);
  }
  
  /**
   * Return the object properties in an array
   */
  function getProps() {
    $result = array();
    foreach(get_object_vars($this) as $key => $value) {
      if ($key[0] != "_") {
        $result[$key] = $value;
      }
    }
    return $result;
  }
  
  /**
   * Get properties specifications
   */
  function getSeeks() {
    return array();
  }

  /**
   * Get seek specifications
   */
  function getSpecs() {
    return array();
  }
  
  /**
   * Get backward reference specifications
   * "collection-name" => "class join-field"
   */
  function getBackRefs() {
    return array (
      "identifiants" => "CIdSante400 object_id",
      "notes"        => "CNote object_id",
      "files"        => "CFile object_id",
      "documents"    => "CCompteRendu object_id",
      "permissions"  => "CPermObject object_id",
      "logs"         => "CUserLog object_id",
    );
  }
  
  /**
   * Liste des champs d'aides à la saisie
   */
  function getHelpedFields() {
    return array();
  }
  
  
  /**
   * Converts string back specifications to objet specifications
   */
  function makeBackSpec($backName) {
    if (array_key_exists($backName, $this->_backSpecs)) {
      return;
    }
    
    $this->_backSpecs[$backName] = new CMbBackSpec($backName, $this->_backRefs[$backName]);
  }

  /**
   * Converts string specifications to objet specifications
   */
  function getSpecsObj($props = null){
    if($props == null){
      $specs =& $this->_props;
      $props = get_object_vars($this);
    }else{
      $specs = $props;
    }
    
    $spec = array();
    foreach($props as $k => $v){
      $spec[$k] = CMbFieldSpecFact::getSpec($this, $k, @$specs[$k]);
    }
    return $spec;
  }

  /**
   * Build Enums variant returning values
   */
  function getEnums() {
    global $AppUI;
    $enums = array();
    foreach ($this->_props as $propName => $propSpec) {
      $specEnum = $this->lookupSpec("enum", $propSpec);
      $specFragments = $this->lookupSpec("list", $propSpec);
      if($specEnum && $specFragments){
        $enums[$propName] = $specFragments;
      }elseif($this->lookupSpec("bool", $propSpec)){
        $enums[$propName][] = 0;
        $enums[$propName][] = 1;
      }
    }
    return $enums;
  }
  
  /**
   * Build Enums variant returning values
   */
  function getEnumsTrans() {
    global $AppUI;
    $enumsTrans = array();
    foreach ($this->_enums as $propName => $enumValues) {
      $enumsTrans[$propName] = array_flip($enumValues);
      foreach($enumsTrans[$propName] as $key => $item) {
        $enumsTrans[$propName][$key] = $AppUI->_(get_class($this).".$propName.$key");
      }
      asort($enumsTrans[$propName]);
    }
    
    return $enumsTrans;
  }
  
  function buildEnums() {
    global $AppUI;
    foreach ($this->_props as $propName => $propSpec) {
      $specEnum = $this->lookupSpec("enum", $propSpec);
      $specFragments = $this->lookupSpec("list", $propSpec);
      if($specEnum && $specFragments){
        $this->_enums[$propName] = $specFragments;
        $this->_enumsTrans[$propName] = array_flip($specFragments);
        foreach($this->_enumsTrans[$propName] as $key => $item) {
          $this->_enumsTrans[$propName][$key] = $AppUI->_($key);
        }
        asort($this->_enumsTrans[$propName]);
      }
    }
  }
  
  /**
   * Functions to check the object's properties
   */
  function lookupSpec($specFragment, $propSpec){
    $aSpecFragments = explode(" ", $propSpec);
    foreach($aSpecFragments as $spec){
      $aFrag = explode("|", $spec);
      $fragmentPosition = array_search($specFragment,$aFrag);
      if($fragmentPosition !== false){
        array_splice($aFrag, $fragmentPosition, 1);
        if(count($aFrag)){
          return $aFrag;
        }else{
          return true;
        }
      }
    }
    return false;
  }
  
  function checkProperty($propName) {
    $specObj = $this->_specs[$propName];
    $msg = null;
    if($msgError = $specObj->checkPropertyValue($this)){
      $msg .= $msgError;
    }
    return $msg;
  }
  
  function checkConfidential($specs = null) {
    global $dPconfig;
    
    if($dPconfig["hide_confidential"]) {
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

  function loadAides($user_id) {
    global $dPconfig;
    
  	foreach ($this->_helped_fields as $field => $prop) {
      $this->_aides[$field] = array();
      $this->_aides[$field]["no_enum"] = null;
      
      if($prop){
        $entryEnums = $this->_enumsTrans[$prop];
        // Création des entrées pour les enums
        $this->_aides[$field]["no_enum"] = null;
        foreach($entryEnums as $valueEnum){
          $this->_aides[$field][$valueEnum] = null;
         }
      }
    }
    
    // Chargement de l'utilisateur courant
    $currUser = new CMediusers();
    $currUser->load($user_id);
    
    // Préparation du chargement des aides
    $where = array();
    
    $where["user_id"] = $this->_spec->ds->prepare("= %", $user_id);
    $where["class"]   = $this->_spec->ds->prepare("= %", $this->_class_name);
    
    
    $order = "name";
    
    // Chargement des Aides de l'utilisateur
    $aides = new CAideSaisie();
    $aides = $aides->loadList($where,$order); 
    $this->orderAides($aides, "Aides du praticien");
    unset($where["user_id"]);
    
    // Chargement des Aides de la fonction de l'utilisateur
    $where["function_id"] = $this->_spec->ds->prepare("= %", $currUser->function_id);
    
    
    $aides = new CAideSaisie();
    $aides = $aides->loadList($where,$order);  
    $this->orderAides($aides, "Aides du cabinet");
  }
  
  function orderAides($aides, $title){
    global $AppUI;
    
    foreach ($aides as $aide) {
      $curr_aide =& $this->_aides[$aide->field];
      
      // Verification de l'existance des clé dans le tableaux
      $linkField = @$this->_helped_fields[$aide->field];
      if($linkField){
        $entryEnums = $this->_enumsTrans[$linkField];
      }
    
      // Ajout de l'aide à la liste générale
      $curr_aide["no_enum"][$title][$aide->text] = $aide->name; 
      
      if(!$aide->depend_value && $linkField){
        // depend de toute les entrées
        foreach($entryEnums as $valueEnum){
          $curr_aide[$valueEnum][$title][$aide->text] = $aide->name;
        }
      }
      
      if($aide->depend_value){
        $curr_aide[$AppUI->_($aide->class.".".$linkField.".".$aide->depend_value)][$title][$aide->text] = $aide->name;
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

/**
 * This function register this object to a templateManager object
 */
    // Register object and references
    function fillTemplate(&$template){
    }
    
    // Register only the fields of this object
    function fillLimitedTemplate(&$template){
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

function htmlReplace($find, $replace, &$source) {
  $matches = array();
  $nbFound = preg_match_all("/$find/", $source, $matches);
  $source = preg_replace("/$find/", $replace, $source);
  return $nbFound;
}


function purgeHtmlText($regexps, &$source) {
  $total = 0;
  foreach ($regexps as $find => $replace) {
    $total += htmlReplace($find, $replace, $source); 
  }
  return $total;
}



?>