<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Data source resource usage log
 */
class CDataSourceLog extends CMbObject {
  public $datasourcelog_id;
  
  // DB Fields
  public $datasource;
  public $requests;
  public $duration;
  
  // Object Reference
  public $accesslog_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table    = 'datasource_log';
    $spec->key      = 'datasourcelog_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["datasource"]   = "str notNull";
    $props["requests"]     = "num";
    $props["duration"]     = "float";
    $props['accesslog_id'] = "ref notNull class|CAccessLog";

    return $props;
  }
  
  /**
   * Fast store using ON DUPLICATE KEY UPDATE MySQL feature
   *
   * @return string Store-like message
   */
  function fastStore() {
    $columns = array();
    $inserts = array();
    $updates = array();

    $fields = $this->getPlainFields();
    unset($fields[$this->_spec->key]);
    foreach ($fields as $_name => $_value) {
      $columns[] = "$_name";
      $inserts[] = "'$_value'";

      if (!in_array($_name, array("datasource", "accesslog_id"))) {
        $updates[] = "$_name = $_name + '$_value'";
      }
    }
    
    $columns = implode(",", $columns);
    $inserts = implode(",", $inserts);
    $updates = implode(",", $updates);
    
    $query = "INSERT INTO datasource_log ($columns) 
      VALUES ($inserts)
      ON DUPLICATE KEY UPDATE $updates";
    $ds = $this->_spec->ds;
    
    if (!$ds->exec($query)) {
      return $ds->error();
    }

    return null;
  }
}
