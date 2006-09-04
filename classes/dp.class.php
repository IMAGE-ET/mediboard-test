<?php /* CLASSES $Id$ */

/**
 *	@package dotproject
 *	@subpackage modules
 *	@version $Revision$
 */

/**
 *	CDpObject Abstract Class.
 *
 *	Parent class to all database table derived objects
 *	@author Andrew Eddie <eddieajau@users.sourceforge.net>
 *	@abstract
 */
class CDpObject {
/**
 *	@var string Name of the table in the db schema relating to child class
 */
	var $_tbl = '';
/**
 *	@var string Name of the primary key field in the table
 */
	var $_tbl_key = '';
/**
 *	@var string Error message
 */
	var $_error = '';
  
/**
 *  @var string default string view
 */
  var $_view = '';
  var $_shortview = '';
  
/**
 *	Object constructor to set table and key field
 *
 *	Can be overloaded/supplemented by the child class
 *	@param string $table name of the table in the db schema relating to child class
 *	@param string $key name of the primary key field in the table
 */
	function CDpObject( $table, $key ) {
		$this->_tbl = $table;
		$this->_tbl_key = $key;
	}
/**
 *	@return string Returns the error message
 */
	function getError() {
		return $this->_error;
	}
/**
 *	Binds a named array/hash to this object
 *
 *	can be overloaded/supplemented by the child class
 *	@param array $hash named array
 *	@return null|string	null is operation was satisfactory, otherwise returns an error
 */
	function bind( $hash ) {
		if (!is_array( $hash )) {
			$this->_error = get_class( $this )."::bind failed.";
			return false;
		} else {
			bindHashToObject( $hash, $this );
			return true;
		}
	}

/**
 *  loads a list of objects matching a SQL where clause
 *  @return the objects array
 */
  function loadList($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    $sql = "SELECT `$this->_tbl`.* FROM `$this->_tbl`";

    // Left join clauses
    if ($leftjoin) {
      assert(is_array($leftjoin));
      foreach ($leftjoin as $table => $condition) {
        $sql .= "\nLEFT JOIN `$table` ON $condition";
      }
    }
    
    // Where clauses
    if (is_array($where)) {
      foreach ($where as $field => $eq) {
        if (is_string($field)) {
          if($pos = strpos($field, ".")) {
            $point_table = substr($field, 0, $pos);
            $point_field = substr($field, $pos + 1);
            $where[$field] = "`$point_table`.`$point_field` $eq";
          } else {
            $where[$field] = "`$field` $eq";
          }
        }
        
        $where[$field] = "(" . $where[$field] . ")";
      }
		}
    
    if ($where) {
      $sql .= "\nWHERE ";
      $sql .= is_array($where) ? implode("\nAND ", $where) : $where;
    }
      
    // Group by fields
    if (is_array($group)) {
      foreach ($group as $key => $field) {
        $group[$key] = "`$field`";
      }
    }
    
    if ($group) {
      $sql .= "\nGROUP BY ";
      $sql .= is_array($group) ? implode(", ", $group) : $group;
    }
      
    // Order by fields
    if (is_array($order)) {
      foreach ($order as $key => $field) {
        // We cannot use the `` syntax because it wont work
        // with table.field syntax, neither the ASC/DESC one
        //$order[$key] = "`$field`";
        $order[$key] = "$field";
      }
    }
    
    if ($order) {
      $sql .= "\nORDER BY ";
      $sql .= is_array($order) ? implode(", ", $order) : $order;
    }
    
    // Limits
    if ($limit) {
			$sql .= "\nLIMIT $limit";
    }

    return db_loadObjectList($sql, $this);
  }

/**
 *  loads the first object matching a SQL where clause
 *  @param string $where the SQL where clause, can also be an array of strings
 *  @param string $order the SQL order clause, can also be an array of strings
 *  @return a copy of the object
 */
  function loadObject($where = null, $order = null, $group = null, $leftjoin = null) {
    $list =& $this->loadList($where, $order, "0,1", $group, $leftjoin);
    foreach ($list as $object) {
      foreach(get_object_vars($object) as $key => $value) {
        $this->$key = $value;
      }
      
      return true;
		}
    
    return false;
  }

/**
 *  Binds an array/hash to this object
 *  @param int $oid optional argument, if not specifed then the value of current key is used
 *  @return any result from the database operation
 */
  function load( $oid=null , $strip = true) {
    $k = $this->_tbl_key;
    if ($oid) {
      $this->$k = intval( $oid );
    }
    $oid = $this->$k;
    if ($oid === null) {
      return false;
    }
    $sql = "SELECT * FROM $this->_tbl WHERE $this->_tbl_key=$oid";
    $object = db_loadObject( $sql, $this, false, $strip );
    $this->updateFormFields();
    return $object;
  }
  
/**
 * This function check if there is confidential fields to crypt
 * Implemented in MbObject
 */
  function checkConfidential($props = null){}
  
/**
 * This function update the form fields from the db fields
 */
	function updateFormFields() {
    $k = $this->_tbl_key;
    $this->_view = $this->_tbl . " #" . $this->$k;
    $this->_shortview = "#" . $this->$k;
	}

/**
 * This functions load all references (bacward an forward) of the object
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
 *	Generic check method
 *
 *	Can be overloaded/supplemented by the child class
 *	@return null if the object is ok
 */
	function check() {
		return NULL;
	}
	
/**
*	Clone de current record
*
*	@author	handco <handco@users.sourceforge.net>
*	@return	object	The new record object or null if error
**/
	function cloneObject() {
		$_key = $this->_tbl_key;
		
		$newObj = $this;
		// blanking the primary key to ensure that's a new record
		$newObj->$_key = '';
		
		return $newObj;
	}


/**
 *	Inserts a new row if id is zero or updates an existing row in the database table
 *
 *	Can be overloaded/supplemented by the child class
 *	@return null|string null if successful otherwise returns and error message
 */
	function store($updateNulls = false) {
    global $AppUI;
    
    // Properties checking
    $this->updateDBFields();
		if($msg = $this->check()) {
			return $AppUI->_(get_class( $this )) . 
        $AppUI->_("::store-check failed:") .
        $AppUI->_($msg);
		}
    
    // DB query
		$k = $this->_tbl_key;
		if( $this->$k ) {
			$ret = db_updateObject( $this->_tbl, $this, $k, $updateNulls );
		} else {
			$ret = db_insertObject( $this->_tbl, $this, $k);
		}
    
		if (!$ret) {
			return get_class( $this )."::store failed <br />" . db_error();
		} 

    // Load the object to get all properties
    $this->load();
    return null;
	}

/**
 * This function update the db fields from the form fields
 */
	function updateDBFields() {
	}

/**
 *	Generic check for whether dependancies exist for this object in the db schema
 *
 *	Can be overloaded/supplemented by the child class
 *	@param string $msg Error message returned
 *	@param int Optional key index
 *	@param array Optional array to compiles standard joins: format [label=>'Label',name=>'table name',idfield=>'field',joinfield=>'field']
 *	@return true|false
 */
	function canDelete( &$msg, $oid=null, $joins=null ) {
		global $AppUI;
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}
		if (is_array( $joins )) {
			$select = "$k";
			$join = "";
			foreach( $joins as $table ) {
				$select .= ",\nCOUNT(DISTINCT {$table['name']}.{$table['idfield']}) AS {$table['idfield']}";
				$join .= "\nLEFT JOIN {$table['name']} " .
            "ON {$table['name']}.{$table['joinfield']} = $this->_tbl.$k";
			}
			$sql = "SELECT $this->_tbl.$select " .
          "FROM $this->_tbl " .
          "$join " .
          "\nWHERE $this->_tbl.$k = '".$this->$k."' GROUP BY $this->_tbl.$k";
    
			$obj = null;
			if (!db_loadObject( $sql, $obj )) {
				$msg = db_error();
				return false;
			}

			$msg = array();
			foreach( $joins as $table ) {
				$k = $table['idfield'];
				if ($obj->$k) {
					$msg[] = $obj->$k. " " . $AppUI->_( $table['label'] );
				}
			}

			if (count( $msg )) {
				$msg = $AppUI->_( "noDeleteRecord" ) . ": " . implode( ', ', $msg );
				return false;
			} else {
				return true;
			}
		}

		return true;
	}

/**
 *	Default delete method
 *
 *	Can be overloaded/supplemented by the child class
 *	@return null|string null if successful otherwise returns and error message
 */
	function delete( $oid=null ) {
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}
    $msg = null;
		if (!$this->canDelete( $msg )) {
			return $msg;
		}

		$sql = "DELETE FROM $this->_tbl WHERE $this->_tbl_key = '".$this->$k."'";
		if (!db_exec( $sql )) {
			return db_error();
		} else {
      $this->$k = null;
			return NULL;
		}
	}

/**
 *	Get specifically denied records from a table/module based on a user
 *	@param int User id number
 *	@return array
 */
	function getDeniedRecords( $uid ) {
		$uid = intval( $uid );
		$uid || exit ("FATAL ERROR<br />" . get_class( $this ) . "::getDeniedRecords failed, user id = 0" );

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
		return db_loadColumn( $sql );
	}

/**
 *	Returns a list of records exposed to the user
 *	@param int User id number
 *	@param string Optional fields to be returned by the query, default is all
 *	@param string Optional sort order for the query
 *	@param string Optional name of field to index the returned array
 *	@param array Optional array of additional sql parameters (from and where supported)
 *	@return array
 */
// returns a list of records exposed to the user
	function getAllowedRecords( $uid, $fields='*', $orderby='', $index=null, $extra=null ) {
		$uid = intval( $uid );
		$uid || exit ("FATAL ERROR<br />" . get_class( $this ) . "::getAllowedRecords failed" );
		$deny = $this->getDeniedRecords( $uid );

		$sql = "SELECT $fields"
			. "\nFROM $this->_tbl, permissions";

		if (@$extra['from']) {
			$sql .= ',' . $extra['from'];
		}
		
		$sql .= "\nWHERE permission_user = $uid"
			. "\n	AND permission_value <> 0"
			. "\n	AND ("
			. "\n		(permission_grant_on = 'all')"
			. "\n		OR (permission_grant_on = '$this->_tbl' AND permission_item = -1)"
			. "\n		OR (permission_grant_on = '$this->_tbl' AND permission_item = $this->_tbl_key)"
			. "\n	)"
			. (count($deny) > 0 ? "\n\tAND $this->_tbl_key NOT IN (" . implode( ',', $deny ) . ')' : '');
		
		if (@$extra['where']) {
			$sql .= "\n\t" . $extra['where'];
		}

		$sql .= ($orderby ? "\nORDER BY $orderby" : '');

		return db_loadHashList( $sql, $index );
	}

/**
 * This function register this object to a templateManager object
 */
    function fillTemplate(&$template){
    }

}
?>
