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
 * SQL query builder
 */
class CRequest {
  var $select = array();
  var $table  = array();
  var $ljoin  = array();
  var $rjoin  = array();
  var $where  = array();
  var $group  = array();
  var $having = array();
  var $order  = array();
  var $forceindex = array();
  var $limit  = "";
  
  /**
   * SELECT [...]
   * 
   * @param mixed $select An array or a string of the SELECT clause
   * 
   * @return CRequest Current request
   */
  function addSelect($select) {
    if (is_array($select)) {
      $this->select = array_merge($this->select, $select);
    }
    elseif (is_string($select)) {
      $this->select[] = $select;
    }
    
    return $this;
  }
  
  /**
   * SELECT [...] AS [...]
   * 
   * @param string $column An column name in the SELECT clause
   * @param string $as     The columns's alias
   * 
   * @return CRequest Current request
   */
  function addColumn($column, $as = null) {
    if ($as) {
      $this->select[$as] = $column; 
    }
    else {
      $this->select[] = $column; 
    }
  }
  
  /**
   * FROM [...]
   * 
   * @param mixed $table An array or a string of the FROM clause
   * 
   * @return CRequest Current request
   */
  function addTable($table) {
    if (is_array($table)) {
      $this->table = array_merge($this->table, $table);
    }
    elseif (is_string($table)) {
      $this->table[] = $table;
    }
    
    return $this;
  }
  
  /**
   * LEFT JOIN [...] ON [...]
   * 
   * @param mixed $ljoin An array or a string of the LEFT JOIN clause
   * 
   * @return CRequest Current request
   */
  function addLJoin($ljoin) {
    if (is_array($ljoin)) {
      $this->ljoin = array_merge($this->ljoin, $ljoin);
    }
    
    return $this;
  }
  
  /**
   * LEFT JOIN [...] ON [...]
   * 
   * @param string $key   The table name of the LEFT JOIN clause
   * @param string $value The conditional expression of the LEFT JOIN clause 
   * 
   * @return CRequest Current request
   */
  function addLJoinClause($key, $value) {
    $this->ljoin[$key] = $value;
    
    return $this;
  }
  
  /**
   * RIGHT JOIN [...] ON [...]
   * 
   * @param mixed $ljoin An array or a string of the RIGHT JOIN statement
   * 
   * @return CRequest Current request
   */
  function addRJoin($ljoin) {
    if (is_array($ljoin)) {
      $this->rjoin = array_merge($this->rjoin, $ljoin);
    }
    
    return $this;
  }
  
  /**
   * RIGHT JOIN [...] ON [...]
   * 
   * @param string $key   The table name of the RIGHT JOIN clause
   * @param string $value The conditional expression of the RIGHT JOIN clause 
   * 
   * @return CRequest Current request
   */
  function addRJoinClause($key, $value) {
    $this->rjoin[$key] = $value;
    
    return $this;
  }
  
  /**
   * WHERE [...]
   * 
   * @param mixed $where An array or a string of the SELECT clause
   * 
   * @return CRequest Current request
   */
  function addWhere($where) {
    if (is_array($where)) {
      $this->where = array_merge($this->where, $where);
    }
    elseif (is_string($where)) {
      $this->where[] = $where;
    }
    
    return $this;
  }
  
  /**
   * WHERE [...]
   * 
   * @param string $key   The field to perform the test on
   * @param string $value The test to be performed
   * 
   * @return CRequest Current request
   */
  function addWhereClause($key, $value) {
    if ($key) {
      $this->where[$key] = $value;
    }
    else {
      $this->where[] = $value;
    }
    
    return $this;
  }

  /**
   * GROUP BY [...]
   * 
   * @param mixed $group An array or a string of the GROUP BY clause
   * 
   * @return CRequest Current request
   */
  function addGroup($group) {
    if (is_array($group)) {
      $this->group = array_merge($this->group, $group);
    }
    elseif (is_string($group)) {
      $this->group[] = $group;
    }
    
    return $this;
  }
  
  /**
   * HAVING [...]
   * 
   * @param mixed $having An array or a string of the HAVING clause
   * 
   * @return CRequest Current request
   */
  function addHaving($having) {
    if (is_array($having)) {
      $this->having = array_merge($this->having, $having);
    }
    elseif (is_string($having)) {
      $this->having[] = $having;
    }
    
    return $this;
  }

  /**
   * ORDER BY [...]
   * 
   * @param mixed $order An array or a string of the ORDER BY clause
   * 
   * @return CRequest Current request
   */
  function addOrder($order) {
    if (is_array($order)) {
      $this->order = array_merge($this->order, $order);
    }
    elseif (is_string($order)) {
      $this->order[] = $order;
    }
    
    return $this;
  }
  
  /**
   * FORCE INDEX [...]
   * 
   * @param mixed $forceindex An array or a string of the FORCE INDEX clause
   * 
   * @return CRequest Current request
   */
  function addForceIndex($forceindex) {
    if (is_array($forceindex)) {
      $this->forceindex = array_merge($this->forceindex, $forceindex);
    }
    elseif (is_string($forceindex)) {
      $this->forceindex[] = $forceindex;
    }
    
    return $this;
  }
  
  /**
   * LIMIT [...]
   * 
   * @param mixed $limit An array or a string of the LIMIT statement
   * 
   * @return CRequest Current request
   */
  function setLimit($limit) {
    $this->limit = $limit;
    
    return $this;
  }
  
  /**
   * Create an artificial limit from an array of results
   * 
   * @param array  $list  The list 
   * @param string $limit The limit, MySQL styled
   * 
   * @return array The slice of the list 
   */
  static function artificialLimit($list, $limit) {
    preg_match("/(?:(\d+),\s*)?(\d+)/", $limit, $matches);
    $offset = intval($matches[1]);
    $length = intval($matches[2]);
    
    return array_slice($list, $offset, $length, true);
  }
  
  /**
   * Returns the SQL query fragment containing everything after the SELECT *
   * 
   * @param string $from The table names
   * 
   * @return string
   */
  function getRequestFrom($from) {
    $sql = "\nFROM $from";
    
    // Force index by fields
    if ($this->forceindex) {
      $sql .= "\nFORCE INDEX (";
      $sql .= is_array($this->forceindex) ? implode(', ', $this->forceindex) : $this->forceindex;
      $sql .= ")";
    }
    
    // Left join clauses
    if ($this->ljoin) {
      assert(is_array($this->ljoin));
      foreach ($this->ljoin as $table => $condition) {
        if (is_string($table)) {
          $sql .= "\nLEFT JOIN `$table` ON $condition";
        }
        else {
          $sql .= "\nLEFT JOIN $condition";
        }
      }
    }
    
    // Right join clauses
    if ($this->rjoin) {
      assert(is_array($this->rjoin));
      foreach ($this->rjoin as $table => $condition) {
        $sql .= "\nRIGHT JOIN `$table` ON $condition";
      }
    }
    
    // Where clauses
    if (is_array($this->where)) {
      $where = $this->where;
      foreach ($where as $field => $eq) {
        if (is_string($field)) {
          $rep = str_replace('.', '`.`', $field);
          $where[$field] = "`$rep` $eq";
        }
        
        $where[$field] = "($where[$field])";
      }
    }
    
    if ($this->where) {
      $sql .= "\nWHERE ";
      $sql .= is_array($this->where) ? implode("\nAND ", $where) : $this->where;
    }
    
    // Group by fields
    if ($this->group) {
      $sql .= "\nGROUP BY ";
      $sql .= is_array($this->group) ? implode(', ', $this->group) : $this->group;
    }
    
    // Having
    if (is_array($this->having)) {
      $having = $this->having;
      foreach ($having as $field => $eq) {
        if (is_string($field)) {
          $rep = str_replace('.', '`.`', $field);
          $having[$field] = "`$rep` $eq";
        }
        
        $having[$field] = "($having[$field])";
      }
    }
    
    if ($this->having) {
      $sql .= "\nHAVING ";
      $sql .= is_array($this->having) ? implode("\nAND ", $having) : $this->having;
    }
    
    // Order by fields
    if ($this->order) {
      $sql .= "\nORDER BY ";
      $sql .= is_array($this->order) ? implode(', ', $this->order) : $this->order;
    }
    
    // Limits
    if ($this->limit) {
      $sql .= "\nLIMIT $this->limit";
    }
    
    return $sql;
  }
  
  /**
   * Returns the SQL string
   * 
   * @param CStoredObject $obj        Object on which table we prefix selects, ne prefix if null
   * @param bool          $found_rows Return the found rows count
   * 
   * @return string
   */
  function getRequest(CStoredObject $obj = null, $found_rows = false) {
    $arraySelect = array();
    $arrayTable = array();
    
    // MbObject binding
    if ($obj) {
      if (count($this->select)) {
        trigger_error("You have to choose either an object or select(s)", E_USER_ERROR);
      }
      
      $arraySelect[] = "`{$obj->_spec->table}`.*";
      
      if (count($this->table)) {
        trigger_error("You have to choose either an object or table(s)");
      }
      
      $arrayTable[] = $obj->_spec->table;
    }
    else {
      $arraySelect = $this->select;
      $arrayTable = $this->table;
    }
    
    // Select clauses
    foreach ($arraySelect as $as => $column) {
      $select[$as] = is_string($as) ? "$column AS `$as`" : $column;
    }
    
    $select = implode(', ', $select);
    
    $sql = $found_rows ? "SELECT SQL_CALC_FOUND_ROWS $select" : "SELECT $select";
    
    // Table clauses
    $table = implode(', ', $arrayTable);
    return $sql . $this->getRequestFrom($table);
  }
  
  /**
   * Returns the SQL string that count the number of rows
   * 
   * @param CStoredObject $obj    Object on which table we prefix selects, one prefix if null
   * @param array         $fields The fields to include in the SELECT clause
   * 
   * @return string A COUNT request
   */
  function getCountRequest(CStoredObject $obj = null, $fields = array()) {
    // MbObject binding
    $sql = "SELECT COUNT(*) as total";
    
    if (is_array($fields) && count($fields)) {
      $sql .= ", ".implode(", ", $fields);
    }
    
    $arrayTable = array();
    if ($obj) {
      if (count($this->table)) {
        trigger_error("You have to choose either an object or table(s)");
      }
      $arrayTable[] = $obj->_spec->table;
    }
    else {
      $arrayTable = $this->table;
    }
    
    // Table clauses
    $table = implode(', ', $arrayTable);
    return $sql . $this->getRequestFrom($table);
  }

  /**
   * Returns the SQL string that count the number of rows
   * 
   * @param CStoredObject $object Object concerned
   * 
   * @return string
   */
  function getIdsRequest(CStoredObject $object) {
    $query = "SELECT `{$object->_spec->table}`.`{$object->_spec->key}`";
    return $query . $this->getRequestFrom($object->_spec->table);
  }
}
