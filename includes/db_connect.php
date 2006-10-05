<?php /* $Id$ */
/**
* Generic functions based on library function (that is, non-db specific)
*
* @todo Encapsulate into a database object
*/

global $AppUI;

$AppUI->getSystemClass("chrono");

$dbChronos = array();

// load the db specific handlers
require_once($AppUI->cfg["root_dir"]."/includes/db_".$AppUI->cfg["dbtype"].".php");

// make the connection to the db
function do_connect($dbid) {
  global $AppUI;
  
  db_connect(
    $dbid,
    $AppUI->cfg["db"][$dbid]["dbhost"],
    $AppUI->cfg["db"][$dbid]["dbname"],
    $AppUI->cfg["db"][$dbid]["dbuser"],
    $AppUI->cfg["db"][$dbid]["dbpass"],
    $AppUI->cfg["db"][$dbid]["dbport"],
    $AppUI->cfg["dbpersist"]
 );
}

do_connect("std");


/**
* This global function loads the first field of the first row returned by the query.
*
* @param string The SQL query
* @param strong The db identifier
* @return The value returned in the query or null if the query failed.
*/
function db_loadResult($sql, $dbid = "std") {
  $cur = db_exec($sql, $dbid);
  $cur or exit(db_error());
  $ret = null;
  if ($row = db_fetch_row($cur)) {
    $ret = $row[0];
  }
  db_free_result($cur);
  return $ret;
}

/**
* This global function loads the first row of a query into an object
*
* If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
* If <var>object</var> has a value of null, then all of the returned query fields returned in the object. 
* @param string The SQL query
* @param object The address of variable
*/
function db_loadObject($sql, &$object) {
  global $mbCacheObjectCount;
  $class = get_class($object);
  if ($object != null) {
    if($object->_id && isset($object->_objectsTable[$object->_id])) {
      bindObjectToObject($object->_objectsTable[$object->_id], $object);
      $mbCacheObjectCount++;
      return true;
    } else {
      $hash = array();
      if(!db_loadHash($sql, $hash)) {
        return false;
      }
      bindHashToObject($hash, $object);
      return true;
    }
  } else {
    $cur = db_exec($sql);
    $cur or exit(db_error());
    if ($object = db_fetch_object($cur)) {
      db_free_result($cur);
      return true;
    } else {
      $object = null;
      return false;
    }
  }
}

/**
* This global function return a result row as an associative array 
*
* @param string The SQL query
* @param array An array for the result to be return in
* @return <b>True</b> is the query was successful, <b>False</b> otherwise
*/
function db_loadHash($sql, &$hash) {
  $cur = db_exec($sql);
  $cur or exit(db_error());
  $hash = db_fetch_assoc($cur);
  db_free_result($cur);
  if ($hash == false) {
    return false;
  } else {
    return true;
  }
}

/**
* Document::db_loadHashList()
*
* { Description }
*
* @param string $index
*/
function db_loadHashList($sql, $index = "") {
  $cur = db_exec($sql);
  $cur or exit(db_error());
  $hashlist = array();
  while ($hash = db_fetch_array($cur)) {
    $hashlist[$hash[$index ? $index : 0]] = $index ? $hash : $hash[1];
  }
  db_free_result($cur);
  return $hashlist;
}

/**
* Document::db_loadList()
*
* return an array of the db lines
* can connect to any database via $dbid param
*
* @param int $maxrows
* @param string the db identifier
* @return array the query result
*/
function db_loadList($sql, $maxrows = null, $dbid = "std") {
  global $AppUI;
  if (!($cur = db_exec($sql, $dbid))) {;
    $AppUI->setMsg(db_error(), UI_MSG_ERROR);
    return false;
  }
  $list = array();
  $cnt = 0;
  while ($hash = db_fetch_assoc($cur)) {
    $list[] = $hash;
    if($maxrows && $maxrows == $cnt++) {
      break;
    }
  }
  db_free_result($cur);
  return $list;
}

/**
* Document::db_loadColumn()
*
* { Description }
*
* @param [type] $maxrows
*/
function db_loadColumn($sql, $maxrows = null) {
  GLOBAL $AppUI;
  if (!($cur = db_exec($sql))) {;
    $AppUI->setMsg(db_error(), UI_MSG_ERROR);
    return false;
  }
  $list = array();
  $cnt = 0;
  while ($row = db_fetch_row($cur)) {
    $list[] = $row[0];
    if($maxrows && $maxrows == $cnt++) {
      break;
    }
  }
  db_free_result($cur);
  return $list;
}

/* return an array of objects from a SQL SELECT query
 * class must implement the Load() factory, see examples in Webo classes
 * @note to optimize request, only select object oids in $sql
 */
function db_loadObjectList($sql, $object, $maxrows = null) {
  global $mbCacheObjectCount;
  $cur = db_exec($sql);
  $list = array();
  $cnt = 0;
  $class = get_class($object);
  $table_key = $object->_tbl_key;
  while ($row = db_fetch_array($cur)) {
    $key = $row[$table_key];
    if(isset($object->_objectsTable[$key])) {
      $list[$key] = $object->_objectsTable[$key];
      $mbCacheObjectCount++;
    } else {
      $newObject = new $class();
      $newObject->bind($row);
      $newObject->checkConfidential();
      $newObject->updateFormFields();
      $object->_objectsTable[$newObject->_id] = $newObject;
      $list[$newObject->_id] = $newObject;
    }
    if($maxrows && $maxrows == $cnt++) {
      break;
    }
  }
  db_free_result($cur);
  return $list;
}


/**
* Document::db_insertArray()
*
* { Description }
*
* @param [type] $verbose
*/
function db_insertArray($table, &$hash, $verbose = false) {
  $fmtsql = "insert into $table (%s) values(%s) ";
  foreach ($hash as $k => $v) {
    if (is_array($v) or is_object($v) or $v == null) {
      continue;
    }
    $fields[] = $k;
    $values[] = "'" . db_escape($v) . "'";
  }
  $sql = sprintf($fmtsql, implode(",", $fields) ,  implode(",", $values));

  ($verbose) && print "$sql<br />\n";

  if (!db_exec($sql)) {
    return false;
  }
  $id = db_insert_id();
  return true;
}

/**
* Document::db_updateArray()
*
* { Description }
*
* @param [type] $verbose
*/
function db_updateArray($table, &$hash, $keyName, $verbose = false) {
  $fmtsql = "UPDATE $table SET %s WHERE %s";
  foreach ($hash as $k => $v) {
    if(is_array($v) or is_object($v) or $k[0] == "_") // internal or NA field
      continue;

    if($k == $keyName) { // PK not to be updated
      $where = "$keyName='" . db_escape($v) . "'";
      continue;
    }
    if ($v == "") {
      $val = "NULL";
    } else {
      $val = "'" . db_escape($v) . "'";
    }
    $tmp[] = "$k=$val";
  }
  $sql = sprintf($fmtsql, implode(",", $tmp) , $where);
  ($verbose) && print "$sql<br />\n";
  $ret = db_exec($sql);
  return $ret;
}

/**
* Document::db_delete()
*
* { Description }
*
*/
function db_delete($table, $keyName, $keyValue) {
  $keyName = db_escape($keyName);
  $keyValue = db_escape($keyValue);
  $sql = "DELETE FROM $table WHERE $keyName='$keyValue'";
  $ret = db_exec($sql);
  return $ret;
}


/**
* Document::db_insertObject()
*
* { Description }
*
* @param [type] $keyName
* @param [type] $verbose
*/
function db_insertObject($table, &$object, $keyName = null, $verbose = false) {
  $fmtsql = "INSERT INTO $table (%s) VALUES (%s) ";
  foreach (get_object_vars($object) as $k => $v) {
    if (is_array($v) or is_object($v) or $v == null) {
      continue;
    }
    if ($k[0] == "_") { // internal field
      continue;
    }
    $fields[] = "`$k`";
    $values[] = "'" . db_escape($v) . "'";
  }
  $sql = sprintf($fmtsql, implode(",", $fields) ,  implode(",", $values));
  ($verbose) && print "$sql<br />\n";

  if (!db_exec($sql)) {
    return false;
  }
  $id = db_insert_id();
  ($verbose) && print "id=[$id]<br />\n";
  if ($keyName && $id)
    $object->$keyName = $id;
  return true;
}

/**
* Document::db_updateObject()
*
* { Description }
*
* @param [type] $updateNulls
*/
function db_updateObject($table, &$object, $keyName, $updateNulls = true) {
  $fmtsql = "UPDATE $table SET %s WHERE %s";
  $tmp = array();
  foreach (get_object_vars($object) as $k => $v) {
    if(is_array($v) or is_object($v) or $k[0] == "_") { // internal or NA field
      continue;
    }
    if($k == $keyName) { // PK not to be updated
      $where = "`$keyName`='" . db_escape($v) . "'";
      continue;
    }
    if ($v === null && !$updateNulls) {
      continue;
    }
    if($v == "") {
      // Tries to nullify empty values but won't fail if not possible
      $val = "NULL";
    } else {
      $val = "'" . db_escape($v) . "'";
    }
    $tmp[] = "`$k`=$val";
  }
  $sql = sprintf($fmtsql, implode(",", $tmp) , $where);
  
  return db_exec($sql);
}

/**
* Document::db_dateConvert()
*
* { Description }
*
*/
function db_dateConvert($src, &$dest, $srcFmt) {
  $result = strtotime($src);
  $dest = $result;
  return ($result != 0);
}

/**
* Document::db_datetime()
*
* { Description }
*
* @param [type] $timestamp
*/
function db_datetime($timestamp = null) {
  if (!$timestamp) {
    return null;
  }
  if (is_object($timestamp)) {
    return $timestamp->toString("%Y-%m-%d %H:%M:%S");
  } else {
    return strftime("%Y-%m-%d %H:%M:%S", $timestamp);
  }
}

/**
* Document::db_dateTime2locale()
*
* { Description }
*
*/
function db_dateTime2locale($dateTime, $format) {
  if (intval($dateTime)) {
    $date = new CDate($dateTime);
    return $date->format($format);
  } else {
    return null;
  }
}

function stripslashes_deep($value) {
  return is_array($value) ?
    array_map("stripslashes_deep", $value) :
    stripslashes($value);
}

/*
* copy the hash array content into the object as properties
* only existing properties of object are filled. when undefined in hash, properties wont be deleted
* @param array the input array
* @param obj byref the object to fill of any class
* @param string
* @param boolean
* @param boolean
*/
function bindHashToObject($hash, &$obj) {
  is_array($hash) or die("bindHashToObject : hash expected");
  is_object($obj) or die("bindHashToObject : object expected");

  foreach (get_object_vars($obj) as $k => $v) {
    if (isset($hash[$k])) {
      $obj->$k = (get_magic_quotes_gpc()) ? stripslashes_deep($hash[$k]) : $hash[$k];
    }
  }
}

function bindObjectToObject($obj1, &$obj) {
  is_object($obj1) or die("bindObjectToObject : object expected");
  is_object($obj) or die("bindObjectToObject : object expected");
  foreach ($obj1->getProps() as $k => $v) {
  //foreach (get_object_vars($obj1) as $k => &$v) {
    $obj->$k = $v;
  }
}
?>
