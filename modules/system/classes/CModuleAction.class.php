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
 * Module-Action link class
 */
class CModuleAction extends CMbObject {
  /** @var integer Primary key */
  public $module_action_id;

  /** @var string Module name */
  public $module;

  /** @var string Action name */
  public $action;

  /** @var CAccessLog[] Logs */
  public $_ref_access_logs;

  /** @var CDataSourceLog[] Logs */
  public $_ref_datasource_logs;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec           = parent::getSpec();
    $spec->loggable = false;
    $spec->table    = 'module_action';
    $spec->key      = 'module_action_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props           = parent::getProps();
    $props["module"] = "str notNull";
    $props["action"] = "str notNull";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps                    = parent::getBackProps();
    $backProps["access_logs"]     = "CAccessLog accesslog_id";
    $backProps["datasource_logs"] = "CDataSourceLog datasourcelog_id";


    return $backProps;
  }

  /**
   * Get all CAccessLog[] backrefs
   *
   * @return CStoredObject[]|null
   */
  function loadRefsAccessLogs() {
    return $this->_ref_access_logs = $this->loadBackRefs("access_logs");
  }

  /**
   * Get all CDataSourceLog[] backrefs
   *
   * @return CStoredObject[]|null
   */
  function loadRefsDataSourceLogs() {
    return $this->_ref_datasource_logs = $this->loadBackRefs("datasource_logs");
  }

  /**
   * Get the ID using ON DUPLICATE KEY UPDATE MySQL feature
   *
   * @return int
   * @throws Exception
   */
  function getID() {
    $module_action_id = SHM::get($this->module . "_" . $this->action);

    if (!$module_action_id) {
      $query = "INSERT INTO `module_action` (`module`, `action`)
                VALUES (?1, ?2)
                ON DUPLICATE KEY UPDATE `module_action_id` = LAST_INSERT_ID(`module_action_id`)";

      $query = $this->_spec->ds->prepare($query, $this->module, $this->action);

      if (!@$this->_spec->ds->exec($query)) {
        throw new Exception("Exec failed");
      }

      $module_action_id = $this->_spec->ds->insertId();
      SHM::put($this->module . "_" . $this->action, $module_action_id);
    }

    return $module_action_id;
  }
}
