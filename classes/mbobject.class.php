<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision$
 *  @author Thomas Despoix
*/

/**
 * Class CMbObject 
 * @abstract Adds Mediboard abstraction layer functionality
 */

global $mbObjectCount;
$mbObjectCount = 0;

require_once("./classes/request.class.php");

require_once("./includes/db_connect.php");
 
class CMbObject {
  
  /**
   * Global properties
   */
  
  var $_tbl       = null; // table name
  var $_tbl_key   = null; // primary key name
  var $_error     = null; // error message
  var $_id        = null; // universal shortcut for the object id
  var $_view      = null; // universal view of the object
  var $_shortview = null; // universal shortview for the object
  var $_canRead   = null; // read permission for the object
  var $_canEdit   = null; // write permission for the object

  /**
   * Properties  specification
   */
  
  var $_aides      = array(); // aides à la saisie
  var $_props      = array(); // properties specifications
  var $_enums      = array(); // enums fields elements
  var $_enumsTrans = array(); // enums fields translated elements
  var $_seek       = array(); // seekable fields
  
  /**
   * References
   */
  
  var $_ref_module    = null; // Parent module
  var $_ref_logs      = null; // history of the object
  var $_ref_first_log = null;
  var $_ref_last_log  = null;

  /**
   * Constructor
   */
 
  function CMbObject($table, $key) {
    global $mbObjectCount;
    $mbObjectCount++;
    
    $this->_tbl = $table;
    $this->_tbl_key = $key;
    $this->_id =& $this->$key;
  }
  
  /**
   * Set/get functions
   */
  function getError() {
    return $this->_error;
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
  
  function loadListWithPerms($permType, $params) {
  }
  
  /**
   * Bind an object with an array
   */

  function bind($hash) {
    if (!is_array($hash)) {
      $this->_error = get_class($this)."::bind failed.";
      return false;
    } else {
      bindHashToObject($hash, $this);
      return true;
    }
  }
  
  /**
   * Object(s) Loaders
   */

  // One object by ID
  function load($oid = null, $strip = true) {
    $k = $this->_tbl_key;
    if ($oid) {
      $this->$k = intval($oid);
    }
    $oid = $this->$k;
    if ($oid === null) {
      return false;
    }
    
    $sql = "SELECT * FROM $this->_tbl WHERE $this->_tbl_key=$oid";
    $object = db_loadObject($sql, $this, false, $strip);

    if (!$object) {
      $this->_id = null;
      return false;
    }

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
    foreach($this->getProps() as $key => $value) {
      if($value !== null) {
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
    foreach($this->getProps() as $key => $value) {
      if($value !== null) {
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
      return true;
    }
    return false;
  }
  
  // Object list by a request constructor
  function loadList($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    $request = new CRequest();
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);
    return db_loadObjectList($request->getRequest($this), $this);
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
    $k = $this->_tbl_key;
    $this->_view = $this->_tbl . " #" . $this->$k;
    $this->_shortview = "#" . $this->$k;
  }
  
  /**
   * References loaders
   */

  function loadRefs() {
    $this->loadRefsBack();
    $this->loadRefsFwd();
  }

  function loadRefsBack() {
  }

  function loadRefsFwd() {
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
  
  function loadRefModule($name) {
    $this->_ref_module = CModule::getInstalled($name);
  }

  /**
   *  Generic check method
   *  @return null if the object is ok a message if not
   */

  function check() {
    global $dPconfig;
    $msg = null;
    $properties = get_object_vars($this);
    $class = get_class($this);
    
    foreach($this->_props as $propName => $propSpec) {
      if(!array_key_exists($propName, $properties)) {
        trigger_error("La spécification cible la propriété '$propName' inexistante dans la classe '$class'", E_USER_WARNING);
      } else {
        $propValue =& $this->$propName;
        if($propValue !== null) {
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
  
  /**
   *  Inserts a new row if id is zero or updates an existing row in the database table
   *  @return null|string null if successful otherwise returns and error message
   */

  function store($updateNulls = false) {
    global $AppUI;
    
    // Properties checking
    $this->updateDBFields();
    if($msg = $this->check()) {
      return $AppUI->_(get_class($this)) . 
        $AppUI->_("::store-check failed:") .
        $AppUI->_($msg);
    }
    
    // The object may not existe in database anymore : re-insert it
    $k = $this->_tbl_key;
    $query = db_prepare("SELECT * FROM `$this->_tbl` WHERE `$k` = %", $this->_id);
    if ($this->_id) {
        if (!db_loadResult($query)) {
        $this->_id = null;
        trigger_error("Store query check failed: $query", E_USER_NOTICE);
      }
    }
    
    // DB query
    if ($this->_id) {
      $ret = db_updateObject($this->_tbl, $this, $k, $updateNulls);
    } else {
      $ret = db_insertObject($this->_tbl, $this, $k);
    }
    
    if (!$ret) {
      return get_class($this)."::store failed <br />" . db_error();
    } 

    // Load the object to get all properties
    $this->load();
    return null;
  }

  /**
   * Generic check for whether dependancies exist for this object in the db schema
   * @param string $msg Error message returned
   * @param int Optional key index
   * @param array Optional array to compiles standard joins: format [label=>'Label',name=>'table name',idfield=>'field',joinfield=>'field']
   * @return true|false
   */
  function canDelete(&$msg, $oid = null, $joins = null) {
    global $AppUI;
    $k = $this->_tbl_key;
    if($oid) {
      $this->$k = intval($oid);
    } else {
      $oid = $this->$k;
    }
    $msgs = array();
    $select = "SELECT $this->_tbl.$k,";
    $from = "\nFROM $this->_tbl ";
    $sql_where  = "\nWHERE $this->_tbl.$k = '$oid'";
    $sql_groupBy = "\nGROUP BY $this->_tbl.$k";
    if (is_array($joins)) {
      foreach($joins as $table) {
        $count = "\nCOUNT(DISTINCT {$table['name']}.{$table['idfield']}) AS number";
        $join = "\nLEFT JOIN {$table['name']} ON {$table['name']}.{$table['joinfield']} = $this->_tbl.$k";
        $join_on = null;
        if(isset($table["joinon"])){
          $join_on = "\nAND " . $table["joinon"];
        }
        $sql = $select . $count . $from . $join . $join_on . $sql_where . $sql_groupBy;
        $obj = null;
        if (!db_loadObject($sql, $obj)) {
          $msg = db_error();
          return false;
        }
        if ($obj->number) {
          $msgs[] = $obj->number. " " . $AppUI->_($table["label"]);
        }
      }
    }
    if (count($msgs)) {
      $msg = $AppUI->_("noDeleteRecord") . ": " . implode(", ", $msgs);
      return false;
    }
    return true;
  }

  /**
   * Default delete method
   * @return null|string null if successful otherwise returns and error message
   */
  function delete($oid = null) {
    $k = $this->_tbl_key;
    if ($oid) {
      $this->$k = intval($oid);
    }
    $msg = null;
    if (!$this->canDelete($msg)) {
      return $msg;
    }
    $sql = "DELETE FROM $this->_tbl WHERE $this->_tbl_key = '".$this->$k."'";
    if (!db_exec($sql)) {
      return db_error();
    } else {
      $this->$k = null;
      return null;
    }
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
    return db_loadObjectList($sql, $this);
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
   * Build Enums variant returning values
   */
  function getEnums() {
    global $AppUI;
    $enums = array();
    foreach ($this->_props as $propName => $propSpec) {
      $specFragments = explode("|", $propSpec);
      if ($this->lookupSpec("enum", $specFragments)) {
        $this->lookupSpec("confidential", $specFragments);
        $this->lookupSpec("notNull", $specFragments);
        $enums[$propName] = $specFragments;
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
      $specFragments = explode("|", $propSpec);
      if ($this->lookupSpec("enum", $specFragments)) {
        $this->lookupSpec("confidential", $specFragments);
        $this->lookupSpec("notNull", $specFragments);
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
  
  function lookupSpec($specFragment, &$specFragments) {
    $fragmentPosition = array_search($specFragment, $specFragments);

    if ($fragmentPosition !== false) {
      array_splice($specFragments, $fragmentPosition, 1);
    }
    
    return $fragmentPosition !== false;
  }
  
  function checkMoreThan($propValue, $specFragments) {
    if ($fragment = @$specFragments[1]) {
  
      switch ($fragment) {
        case "moreThan":
        $targetPropName = $specFragments[2];
        $targetPropValue = $this->$targetPropName;
    
        if (!isset($targetPropValue)) {
          return printf("Elément cible invalide ou inexistant (nom = %s)", $targetPropName);
        }

        if ($propValue <= $targetPropValue) {
          return "'$propValue' n'est pas strictement supérieur à '$targetPropValue'";
        }
  
        break;
             
        case "moreEquals":
        $targetPropName = $specFragments[2];
        $targetPropValue = $this->$targetPropName;
    
        if (!isset($targetPropValue)) {
          return printf("Elément cible invalide ou inexistant (nom = %s)", $targetPropName);
        }

        if ($propValue < $targetPropValue) {
          return "'$propValue' n'est pas supérieur ou égal à '$targetPropValue'";
        }
  
        break;
      }
    };
  
    return null;
  }
  
  function checkProperty($propName) {
    $propValue =& $this->$propName;
    $propSpec =& $this->_props[$propName];
    $specFragments = explode("|", $propSpec);
    
    // remove confidential status
    $confidential = array_search("confidential", $specFragments);
    if ($confidential !== false) {
      array_splice($specFragments, $confidential, 1);
    }

    // notNull
    $notNull = array_search("notNull", $specFragments);
    if ($notNull !== false) {
      array_splice($specFragments, $notNull, 1);
    }

    if(!in_array("xor", $specFragments) and !in_array("nand", $specFragments)){
      if ($propValue === null || $propValue === "") {
        return $notNull ? "Ne pas peut pas avoir une valeur nulle" : null;
      }
    }
    switch ($specFragments[0]) {
      // Reference to another object
      case "ref":
        if (!is_numeric($propValue) && $propValue!="") {
          return "N'est pas une référence (format non numérique)";
        }

        $propValue = intval($propValue);
        
        if ($propValue === 0 and $notNull) {
          return "ne peut pas être une référence nulle";
        }

        if ($propValue < 0) {
          return "N'est pas une référence (entier négatif)";
        }
        
        if(isset($specFragments[1])){
        
          switch ($specFragments[1]) {
            case "xor":
              $targetPropName = @$specFragments[2];
              
              if(!$targetPropName){
                return "Spécification de chaîne de caractères invalide";
              }
          
              $targetPropValue = $this->$targetPropName;
      
              if (!isset($targetPropValue)) {
                return "Elément cible invalide ou inexistant (nom = $targetPropName)";
              }
             
              if ($propValue==0 and $targetPropValue==0) {
                return "Merci de choisir soit '$propName', soit '$targetPropName'"; 
              }
              
              if ($propValue!=0 and $targetPropValue!=0) {
                return "Vous ne devez choisir qu'un seul de ces champs : '$propName', '$targetPropName'"; 
              }
            
              break;
            
            case "nand":
          	  break;
          	
            default:
              return "Spécification de chaîne de caractères invalide";
          }
          break;
        }
      // regular string
      case "str":
        switch (@$specFragments[1]) {
          case null:
            break;
            
          case "length":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur invalide (longueur = $length)";
            }
            
            if (strlen($propValue) != $length) {
              return "N'a pas la bonne longueur (longueur souhaitée : $length)'";
            }
            
            break;
            
          case "minLength":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur minimale invalide (longueur = $length)";
            }
            
            if (strlen($propValue) < $length) {
              return "N'a pas la bonne longueur (longueur minimale souhaitée : $length)'";
            }
            
            break;
            
          case "maxLength":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur minimale invalide (longueur = $length)";
            }
            
            if (strlen($propValue) > $length) {
              return "N'a pas la bonne longueur (longueur maximale souhaitée : $length)'";
            }
            
            break;
        
          default:
            return "Spécification de chaîne de caractères invalide";
        }
        
        break;

      // numerical string
      case "num":
        if (!is_numeric($propValue)) {
          return "N'est pas une chaîne numérique";
        }
      
        switch (@$specFragments[1]) {
          case null:
            break;
            
          case "min":
            if (!is_numeric($min = @$specFragments[2])) {
              return "Spécification de minimum numérique invalide";
            }
            
            $min = intval($min);
            if ($propValue < $min) {
              return "Soit avoir une valeur minimale de $min";
            }
            
            break;

          case "max":
            if (!is_numeric($max = @$specFragments[2])) {
              return "Spécification de maximum numérique invalide";
            }
            
            $max = intval($max);
            if ($propValue > $max) {
              return "Soit avoir une valeur maximale de $max";
            }
            
            break;
          
          case "pos":            
            if ($propValue <= 0) {
              return "Doit avoir une valeur positive";
            }
            
            break;
  
          case "length":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur invalide (longueur = $length)";
            }
            
            if (strlen($propValue) != $length) {
              return "N'a pas la bonne longueur (longueur souhaité : $length)'";
            }
            
            break;
            
          case "minLength":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur minimale invalide (longueur = $length)";
            }
            
            if (strlen($propValue) < $length) {
              return "N'a pas la bonne longueur (longueur minimale souhaitée : $length)'";
            }
            
            break;
            
          case "maxLength":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur minimale invalide (longueur = $length)";
            }
            
            if (strlen($propValue) > $length) {
              return "N'a pas la bonne longueur (longueur maximale souhaitée : $length)'";
            }
            
            break;
        }
        
        break;
      
      // Enumeration
      case "enum":
        array_shift($specFragments);
        if (!in_array($propValue, $specFragments)) {
          return "N'a pas une valeur possible";
        }

        break;
    
      // Date
      case "date":
        if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $propValue)) {
          return "format de date invalide";
        }
        
        break;
    
      // Time
      case "time":
        if (!preg_match ("/^([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2}))?$/", $propValue)) {
          return "format de time invalide";
        }
        
        break;
    
      // DateTime
      case "dateTime":
        if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})[ \+]([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/", $propValue)) {
          return "format de dateTime invalide";
        }
        
        break;
    
      // Currrency format
      case "currency":
        //if (!preg_match ("/^([0-9]+)(\.[0-9]{0,2}){0,1}$/", $propValue)) {
        if(!is_numeric($propValue)){
          return "n'est pas une valeur décimale (utilisez le . pour la virgule)";
        }
        
        switch (@$specFragments[1]) {
          case null:
            break;
            
          case "min":
            if (!is_numeric($min = @$specFragments[2])) {
              return "Spécification de minimum numérique invalide";
            }
            
            $min = intval($min);
            if ($propValue < $min) {
              return "Soit avoir une valeur minimale de $min";
            }
            
            break;

          case "max":
            if (!is_numeric($max = @$specFragments[2])) {
              return "Spécification de maximum numérique invalide";
            }
            
            $max = intval($max);
            if ($propValue > $max) {
              return "Soit avoir une valeur maximale de $max";
            }
            
            break;
          
          case "pos":            
            if ($propValue <= 0) {
              return "Doit avoir une valeur positive";
            }
            
            break;
          
          case "minMax":
            if (!is_numeric($min = @$specFragments[2]) || !is_numeric($max = @$specFragments[3])) {
              return "Spécification de maximum numérique invalide";
            } 
            if($propValue>$max || $propValue<$min){
              return "N'est pas compris entre $min et $max";
            }
            break;
            
        }     
        
        break;
    
      // Percentage with two digits after coma
      case "pct":
        if (!preg_match ("/^([0-9]+)(\.[0-9]{0,2}){0,1}$/", $propValue)) {
          return "n'est pas un pourcentage (utilisez le . pour la virgule)";
        }
        
        break;
        
      // Text free format
      case "text":
        break;
        
      // HTML Text
      case "html":
        // @todo Should validate against XHTML DTD
        
        // Purges empty spans
        $regexps = array (
          "<span[^>]*>[\s]*<\/span>" => " ",
          "<font[^>]*>[\s]*<\/font>" => " ",
          "<span class=\"field\">([^\[].*)<\/span>" => "$1"
          );
        
//         while (purgeHtmlText($regexps, $propValue));

        break;
      
      case "email":
        if (!preg_match("/^[-a-z0-9\._]+@[-a-z0-9\.]+\.[a-z]{2,4}$/i", $propValue)) {
          return "Le format de l'email n'est pas valide";
        }
        break;
      // Special Codes
      case "code":
        switch (@$specFragments[1]) {
          case "ccam":
            if (!preg_match ("/^([a-z0-9]){0,7}$/i", $propValue)) {
              return "Code CCAM incorrect, doit contenir 4 lettres et trois chiffres";
            }
            
            break;

          case "cim10":
            if (!preg_match ("/^([a-z0-9]){0,5}$/i", $propValue)) {
              return "Code CCAM incorrect, doit contenir 5 lettres maximum";
            }
            
            break;

          case "adeli":
            if (!preg_match ("/^([0-9]){9}$/i", $propValue)) {
              return "Code Adeli incorrect, doit contenir exactement 9 chiffres";
            }
            
            break;

          case "insee":
            $matches = null;
            if (!preg_match ("/^([1-2][0-9]{2}[0-9]{2}[0-9]{2}[0-9]{3}[0-9]{3})([0-9]{2})$/i", $propValue, $matches)) {
              return "Matricule incorrect, doit contenir exactement 15 chiffres (commençant par 1 ou 2)";
            }
          
            $code = $matches[1];
            $cle = $matches[2];
            
            // Use bcmod since standard modulus can't work on numbers exceedind the 2^32 limit
            if (function_exists("bcmod")) {
              if (97 - bcmod($code, 97) != $cle) {
                return "Matricule incorrect, la clé n'est pas valide";
              }
            }
          
            break;

          default:
            return "Spécification de code invalide";
        }

        break;

      default:
        return "Spécification invalide";
    }

    if ($checkMessage = $this->checkMoreThan($propValue, $specFragments)) {
      return $checkMessage;
    }
    
    return null;
  }
  
  function checkConfidential($props = null) {
    global $dPconfig;
    if($dPconfig["hide_confidential"]) {
      if($props == null)
        $props = $this->_props;
      foreach ($props as $propName => $propSpec) {
        $propValue =& $this->$propName;
        if ($propValue !== null) {
          $this->codeProperty($propValue, $propSpec);
        }
      }
    }
  }
  
  function randomString($array, $length) {
    $key = "";
    $count = count($array) - 1;
    srand((double)microtime()*1000000);
    for($i = 0; $i < $length; $i++) $key .= $array[rand(0, $count)];
    return($key);
  }

  function codeProperty(&$propValue, &$propSpec) {
    $chars = array(
      "a","b","c","d","e","f","g","h","i","j","k","l","m",
      "n","o","p","q","r","s","t","u","v","w","x","y","z");
    $nums = array("0","1","2","3","4","5","6","7","8","9");
    $days = array();
    for($i = 1; $i < 29; $i++) {
      if($i < 10)
        $days[] = "0".$i;
      else
        $days[] = $i;
    }
    $monthes = array(
      "01","02","03","04","05","06","07","08","09", "10", "11", "12");
    $hours = array();
    for($i = 9; $i < 18; $i++) {
      if($i < 10)
        $hours[] = "0".$i;
      else
        $hours[] = $i;
    }
    $mins = array();
    for($i = 0; $i < 60; $i++) {
      if($i < 10)
        $mins[] = "0".$i;
      else
        $mins[] = $i;
    }
    
    $defaultLength = 6;

    $specFragments = explode("|", $propSpec);
    
    // test if it is confidential
    $confidential = array_search("confidential", $specFragments);
    if ($confidential !== false) {
      array_splice($specFragments, $confidential, 1);
    }

    if ($confidential) {
      // test if notNull and remove this fragment
      $notNull = array_search("notNull", $specFragments);
      if ($notNull !== false) {
        array_splice($specFragments, $notNull, 1);
      }
      
      switch ($specFragments[0]) {
        // Reference to another object : do nothing
        case "ref":
          break;
          
        // regular string
        case "text": 
          $propValue = $this->randomString($chars, 40);
          break;
          
        // regular string
        case "str":
          switch (@$specFragments[1]) {
            case null:
              $propValue = $this->randomString($chars, $defaultLength);
              break;
              
            case "length":
              $length = intval(@$specFragments[2]);
              $propValue = $this->randomString($chars, $length);
              break;
              
            case "minLength":
              $length = intval(@$specFragments[2]);
              if($defaultLength < $length)
                $propValue = $this->randomString($chars, $length);
              else
                $propValue = $this->randomString($chars, $defaultLength);
              break;
              
            case "maxLength":
              $length = intval(@$specFragments[2]);
              if($defaultLength > $length)
                $propValue = $this->randomString($chars, $length);
              else
                $propValue = $this->randomString($chars, $defaultLength);
              break;
          
            default:
              $propValue = null;
          }
          
          break;
  
        // numerical string
        case "num":
          switch (@$specFragments[1]) {
            case null:
              $propValue = $this->randomString($nums, $defaultLength);
              break;
              
            case "length":
              $length = intval(@$specFragments[2]);
              $propValue = $this->randomString($nums, $length);
              break;
              
            case "minLength":
              $length = intval(@$specFragments[2]);
              if($defaultLength < $length)
                $propValue = $this->randomString($nums, $length);
              else
                $propValue = $this->randomString($nums, $defaultLength);
              break;
              
            case "maxLength":
              $length = intval(@$specFragments[2]);
              if($defaultLength > $length)
                $propValue = $this->randomString($nums, $length);
              else
                $propValue = $this->randomString($nums, $defaultLength);
              break;
          
            default:
              $propValue = null;
          }
          
          break;
        
        // Enumeration
        case "enum":
          array_shift($specFragments);
          $propValue = $this->randomString($specFragments, 1);
          break;
      
        // Date
        case "date":
          $propValue = "19".$this->randomString($nums, 2)."-".$this->randomString($monthes, 1)."-".$this->randomString($days, 1);
          break;
      
        // Time
        case "time":
          $propValue = $this->randomString($hours, 1).":".$this->randomString($mins, 1).":".$this->randomString($mins, 1);
          break;
      
        // DateTime
        case "dateTime":
          $propValue = "19".$this->randomString($nums, 2)."-".$this->randomString($monthes, 1)."-".$this->randomString($days, 1);
          $propValue .= " ".$this->randomString($hours, 1).":".$this->randomString($mins, 1).":".$this->randomString($mins, 1);
          break;
      
        // Format monétaire
        case "currency":
          $propValue = $this->randomString($nums, 2).".".$this->randomString($nums, 2);
          break;
          
        // HTML Text
        case "html":
          $propValue = "Document confidentiel";
          break;
  
        default:
          return "Spécification invalide";
      }
    }
    return null;
  }

  function loadAides($user_id) {
    $class = get_class($this);
    // Initialisation to prevent understandable smarty notices
    foreach($this->_props as $propName => $propSpec) {
      $specFragments = explode("|", $propSpec);
      if (array_search("text", $specFragments) !== false) {
        $this->_aides[$propName] = null;
      }
    }
    // Load appropriate Aides
    $where = array();
    $where["user_id"] = " = '$user_id'";
    $where["class"] = " = '$class'";
    $order = "name";
    $aides = new CAideSaisie();
    $aides = $aides->loadList($where,$order);  
    // Aides mapping suitable for select options
    foreach ($aides as $aide) {
      $this->_aides[$aide->field][$aide->text] = $aide->name;  
    }
  }

  /**
   * Get specifically denied records from a table/module based on a user
   * @param int User id number
   * @return array
   */

  function getDeniedRecords($uid) {
    $uid = intval($uid);
    $uid || exit ("FATAL ERROR<br />" . get_class($this) . "::getDeniedRecords failed, user id = 0");

    // get read denied projects
    $deny = array();
    $sql = "
    SELECT $this->_tbl_key
    FROM $this->_tbl, permissions
    WHERE permission_user = $uid
      AND permission_grant_on = '$this->_tbl'
      AND permission_item = $this->_tbl_key
      AND permission_value = 0
    ";
    return db_loadColumn($sql);
  }

  /**
   * Returns a list of records exposed to the user
   * @param int User id number
   * @param string Optional fields to be returned by the query, default is all
   * @param string Optional sort order for the query
   * @param string Optional name of field to index the returned array
   * @param array Optional array of additional sql parameters (from and where supported)
   * @return array
   */

  // returns a list of records exposed to the user
  function getAllowedRecords($uid, $fields = "*", $orderby = "", $index = null, $extra = null) {
    $uid = intval($uid);
    $uid || exit ("FATAL ERROR<br />" . get_class($this) . "::getAllowedRecords failed");
    $deny = $this->getDeniedRecords($uid);

    $sql = "SELECT $fields"
      . "\nFROM $this->_tbl, permissions";

    if (@$extra["from"]) {
      $sql .= "," . $extra["from"];
    }
    
    $sql .= "\nWHERE permission_user = $uid"
      . "\n AND permission_value <> 0"
      . "\n AND ("
      . "\n   (permission_grant_on = 'all')"
      . "\n   OR (permission_grant_on = '$this->_tbl' AND permission_item = -1)"
      . "\n   OR (permission_grant_on = '$this->_tbl' AND permission_item = $this->_tbl_key)"
      . "\n )"
      . (count($deny) > 0 ? "\n\tAND $this->_tbl_key NOT IN (" . implode(",", $deny) . ")" : "");
    
    if (@$extra["where"]) {
      $sql .= "\n\t" . $extra["where"];
    }

    $sql .= ($orderby ? "\nORDER BY $orderby" : "");

    return db_loadHashList($sql, $index);
  }
  
  function loadLogs($type = null){
    $class = get_class($this);
    $key = $this->_tbl_key;
    $obj_id = $this->$key;
    $where = array();
    
    if($obj_id !== "" && $obj_id !== null)
      $where["object_id"] = "= '$obj_id'";
    if($class)
      $where["object_class"] = "= '$class'";
    if($type)
      $where["type"] = "= '$type'";
    $order = "date ASC";
    $list = new CUserLog;
    $list = $list->loadList($where, $order, "0, 100");
    foreach($list as $key => $value) {
      $list[$key]->loadRefsFwd();
    }   
    $this->_ref_logs  = $list;
    $this->_ref_first_log = reset($list);
    $this->_ref_last_log  = end($list);
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

//  echo "<h1>Total found: $total<h1><hr />";
  
  return $total;
}
?>