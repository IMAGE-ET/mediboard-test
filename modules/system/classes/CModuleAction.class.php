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
class CModuleAction extends CStoredObject {
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
    $backProps                            = parent::getBackProps();
    $backProps["access_logs"]             = "CAccessLog module_action_id";
    $backProps["datasource_logs"]         = "CDataSourceLog module_action_id";
    $backProps["access_logs_archive"]     = "CAccessLogArchive module_action_id";
    $backProps["datasource_logs_archive"] = "CDataSourceLogArchive module_action_id";

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
   * @param string $module Specified module
   * @param string $action Specified action
   *
   * @throws Exception
   * @return int
   */
  static function getID($module, $action) {
    $cache = new Cache(__METHOD__, func_get_args(), Cache::INNER_OUTER);
    if ($module_action_id = $cache->get()) {
      return $module_action_id;
    }

    $query = "INSERT INTO `module_action` (`module`, `action`)
              VALUES (?1, ?2)
              ON DUPLICATE KEY UPDATE `module_action_id` = LAST_INSERT_ID(`module_action_id`)";

    $self = new self;
    $ds = $self->_spec->ds;
    $query = $ds->prepare($query, $module, $action);

    if (!@$ds->exec($query)) {
      throw new Exception("Exec failed");
    }

    $module_action_id = $ds->insertId();
    return $cache->put($module_action_id);
  }

  /**
   * Get actions for given module
   *
   * @param string $module Module name
   *
   * @return ref[] Array of actions with actions as keys and ids as values
   */
  static function getActions($module) {
    static $modules_actions;
    if (!$modules_actions) {
      $request = new CRequest();
      $request->addColumn("module");
      $request->addColumn("action");
      $request->addColumn("module_action_id");
      $self            = new self;
      $ds              = $self->_spec->ds;
      $modules_actions = $ds->loadTree($request->makeSelect($self));
    }

    return $modules_actions[$module];
  }
}
