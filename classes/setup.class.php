<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Romain Ollivier
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
/**
 * Setup abstract class
 * Install, upgrade or remove modules
 */ 
class CSetup {
  // Public vars
  var $mod_name = null;
  var $mod_version = null;
  var $ds = null;
  var $mod_type = "user";

  // Protected vars
  var $revisions = array();
  var $queries = array();
  var $functions = array();
  var $dependencies = array();
  var $timeLimit = array();
  var $tables = array();
  
  function __construct() {
  	$this->ds = CSQLDataSource::get("std");
  }

  /**
   * Creates a revision of a given name
   * @param string $revision Revision number of form x.y
   */
  function makeRevision($revision) {
  	 
    if (in_array($revision, $this->revisions)) {
      trigger_error("Revision '$revision' already exists", E_USER_ERROR);
    }
    
    $this->revisions[] = $revision;
    $this->queries[$revision] = array();
    $this->functions[$revision] = array();
    $this->dependencies[$revision] = array();
    $this->timeLimit[$revision] = null;
    end($this->revisions);
  }
  
  /**
   * Add a callback function to be executed
   * function must return true/false
   */
  function addFunctions($function) {
    $this->functions[current($this->revisions)][] = $function;
  }
  
  /**
   * Set a time limit for un actual upgrade
   * @param int $limit Limits in seconds
   */
  function setTimeLimit($limit){
    $this->timeLimit[current($this->revisions)] = $limit;
  }
  
  /**
   * Associates an SQL query to a module revision
   * @param string $query SQL query
   */
  function addQuery($query) {
    // Table creation ?
    if (preg_match("/CREATE\s+TABLE\s+(\S+)/i", $query, $matches)) {
      $table = trim($matches[1], "`");
      $this->addTable($table);
    }
    // Table name changed ?
    if (preg_match("/RENAME\s+TABLE\s+(\S+)\s+TO\s+(\S+)/i", $query, $matches)) {
      $tableFrom = trim($matches[1], "`");
      $tableTo   = trim($matches[2], "`");
      $this->renameTable($tableFrom, $tableTo);
    }
    // Table removed ?
    if (preg_match("/DROP\s+TABLE\s+(\S+)/i", $query, $matches)) {
      $table = trim($matches[1], "`");
      $this->dropTable($table);
    }
    
    $this->queries[current($this->revisions)][] = $query;
  }
  
  /**
   * Add a preference query to current revision definition 
   * @param string $name Name of the preference
   * @param string $default Default value of the preference
   */
  function addPrefQuery($name, $default) {
    $sql = "SELECT FROM `user_preferences` WHERE `pref_user` = '0' && `pref_name` = '$name' && `pref_value` = '$default'";
    $result = $this->ds->exec($sql);
    if(!$this->ds->numRows($result)) {
      $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )
        VALUES ('0', '$name', '$default');";
      $this->addQuery($sql);
    }
  }
  
  /**
   * Registers a table in the module
   * @param string $table Table name
   */
  function addTable($table) {
    if (in_array($table, $this->tables)) {
      trigger_error("Table '$table' already exists", E_USER_ERROR);
    }
    $this->tables[] = $table;
  }

  /**
   * Remove a table in the module
   * @param string $table Table name
   */
  function dropTable($table) {
    CMbArray::removeValue($table, $this->tables);
  }

  /**
   * Change a table name in the module
   * @param string $tableFrom Table former name
   * @param string $tableFrom Table latter name
   */
  function renameTable($tableFrom, $tableTo) {
    $this->dropTable($tableFrom);
    $this->addTable($tableTo);
  }
    
  /**
   * Adds a revision dependency with another module
   * @param string $module
   * @param string $revision
   */
  function addDependency($module, $revision) {
    $dependency = new CObject;
    $dependency->module = $module;
    $dependency->revision = $revision;
    $this->dependencies[current($this->revisions)][] = $dependency;
  }
     
  /**
   * Launches module upgrade process
   * @param string $oldRevision revision befoire upgrade
   */
  function upgrade($oldRevision) {
    global $AppUI;

    if (array_key_exists($this->mod_version, $this->queries)) {
      $AppUI->setMsg("Latest revision '$this->mod_version' should not have upgrade queries", UI_MSG_ERROR);
      return;
    }

    if (!array_key_exists($oldRevision, $this->queries)) {
      $AppUI->setMsg("No queries for '$this->mod_name' setup at revision '$oldRevision'", UI_MSG_ERROR);
      return;
    }
    
    // Point to the current revision
    reset($this->revisions);
    while ($oldRevision != $currRevision = current($this->revisions)) {
      next($this->revisions);
    }

    do {
      // Check for dependencies
      foreach ($this->dependencies[$currRevision] as $dependency) {
        $module = @CModule::getInstalled($dependency->module);
        if (!$module || $module->mod_version < $dependency->revision) {
          $depFailed = true;
          $AppUI->setMsg("Failed module depency for '$dependency->module' at revision '$dependency->revision'", UI_MSG_WARNING, true);
        }
      }
        
      if (@$depFailed) {
        return $currRevision;
      }
      
      // Set Time Limit
      if($this->timeLimit[$currRevision]){
        set_time_limit($this->timeLimit[$currRevision]);
      }

      // Query upgrading
      foreach ($this->queries[$currRevision] as $query) {
        if (!$this->ds->exec($query)) {
          $AppUI->setMsg("Error in queries for revision '$currRevision': see logs.", UI_MSG_ERROR);
          return $currRevision;
        }
      }
      
      // Callback upgrading
      foreach ($this->functions[$currRevision] as $function) {
        if (!call_user_func($function)) {
          $AppUI->setMsg("Error in function '$function' call back for revision '$currRevision': see logs.", UI_MSG_ERROR);
          return $currRevision;
        }
      }

    } while ($currRevision = next($this->revisions));

    return $this->mod_version;
  }
  
  /**
   * Removes a module
   * Warning, it actually breaks module dependency
   * @return bool Job done
   */
  function remove() {
    global $AppUI;
    
    if ($this->mod_type == "core") {
      $AppUI->setMsg("Impossible de supprimer le module '%s'", UI_MSG_ERROR, $this->mod_name);
      return false;
    }
    
    $success = true;
    foreach ($this->tables as $table) {
      $query = "DROP TABLE `$table`";
      if (!$this->ds->exec($query)) {
        $success = false;
        $AppUI->setMsg("Failed to remove table '%s'", UI_MSG_ERROR, $table);
      } 
    }
    
    return $success;
  }
  
  /**
   * Link to the configure pane
   * Should be handled in the template
   */
  function configure() {
    global $AppUI;
    $AppUI->redirect("m=$this->mod_name&a=configure" );
    return true;
  }  
}
?>