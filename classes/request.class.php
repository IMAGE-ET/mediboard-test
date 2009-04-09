<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Request
class CRequest {
  
  // params
  var $select = array();
  var $table  = array();
  var $ljoin  = array();
  var $rjoin  = array();
  var $where  = array();
  var $group  = array();
  var $order  = array();
  var $limit  = "";
    
  function CRequest() {
  }
  
  function resetParams() {
    $this->select = array();
    $this->ljoin  = array();
    $this->rjoin  = array();
    $this->where  = array();
    $this->group  = array();
    $this->order  = array();
    $this->limit  = "";
  }
  
  
  function addSelect($select) {
    if(is_array($select)) {
      $this->select = array_merge($this->select, $select);
    } elseif(is_string($select)) {
      $this->select[] = $select;
    }
  }
  
  function addColumn($column, $as = null) {
    if ($as) {
      $this->select[$as] = $column; 
    } else {
      $this->select[] = $column; 
    }
  }

  function addTable($table) {
    if(is_array($table)) {
      $this->table = array_merge($this->table, $table);
    } elseif(is_string($table)) {
      $this->table[] = $table;
    }
  }

  function addLJoin($ljoin) {
    if(is_array($ljoin)) {
      $this->ljoin = array_merge($this->ljoin, $ljoin);
    }
  }

  function addLJoinClause($key, $value) {
    $this->ljoin[$key] = $value;
  }

  function addRJoin($ljoin) {
    if(is_array($ljoin)) {
      $this->rjoin = array_merge($this->rjoin, $ljoin);
    }
  }

  function addRJoinClause($key, $value) {
    $this->rjoin[$key] = $value;
  }

  function addWhere($where) {
    if(is_array($where)) {
      $this->where = array_merge($this->where, $where);
    } elseif(is_string($where)) {
      $this->where[] = $where;
    }
  }
  
/**
 * @param string $key the field to perform the test on
 * @param string $value the test to be performed
 */
  function addWhereClause($key, $value) {
    if($key) {
      $this->where[$key] = $value;
    } else {
      $this->where[] = $value;
    }
  }

  function addGroup($group) {
    if(is_array($group)) {
      $this->group = array_merge($this->group, $group);
    } elseif(is_string($group)) {
      $this->group[] = $group;
    }
  }

  function addOrder($order) {
    if(is_array($order)) {
      $this->order = array_merge($this->order, $order);
    } elseif(is_string($order)) {
      $this->order[] = $order;
    }
  }

  function setLimit($limit) {
    $this->limit = $limit;
  }
  
  /**
   * returns the SQL query fragment containing everuthing after the SELECT *
   * @param $from The table names
   */
  function getRequestFrom($from) {
    $sql = "\nFROM $from";

    // Left join clauses
    if ($this->ljoin) {
      assert(is_array($this->ljoin));
      foreach ($this->ljoin as $table => $condition) {
        $sql .= "\nLEFT JOIN `$table` ON $condition";
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
   * returns the SQL string
   * @param CMbObject $obj Object on which table we prefix selects, ne prefix if null
   */
  function getRequest($obj = null) {

    $arraySelect = array();
    $arrayTable = array();

    // MbObject binding
    if ($obj) {
    // Removed for performance tests
    //  if (!$obj instanceof CMbObject)) {
    //    trigger_error("Object must be an instance of MbObject", E_USER_ERROR);
    //  }
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
    $sql = "SELECT $select";
    
    // Table clauses
    $table = implode(', ', $arrayTable);
    return $sql . $this->getRequestFrom($table);
  }

  /**
   * returns the SQL string that count the number of rows
   * @param CMbObject $obj Object on which table we prefix selects, ne prefix if null
   */
  function getCountRequest($obj = null) {
    // MbObject binding
    $sql = "SELECT COUNT(*) as total";
    $arrayTable = array();
    if ($obj) {
      if (count($this->table)) {
        trigger_error("You have to choose either an object or table(s)");
      }
      $arrayTable[] = $obj->_spec->table;
    } else {
      $arrayTable = $this->table;
    }
    
    // Table clauses
    $table = implode(', ', $arrayTable);
    return $sql . $this->getRequestFrom($table);
  }
}
?>