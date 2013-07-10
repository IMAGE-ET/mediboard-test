<?php

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Error log data
 */
class CErrorLogData extends CMbObject {
  public $error_log_data_id;

  public $value;
  public $value_hash;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "error_log_data";
    $spec->key    = "error_log_data_id";
    $spec->loggable = false;
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["value"]      = "text notNull";
    $props["value_hash"] = "str notNull";
    return $props;
  }

  static function insert($value) {
    $ds = CSQLDataSource::get("std");
    if (!$ds) {
      throw new Exception("No datasource available");
    }

    $query = "INSERT INTO `error_log_data` (`value`, `value_hash`)
    VALUES (?1, ?2)
    ON DUPLICATE KEY UPDATE `error_log_data_id` = LAST_INSERT_ID(`error_log_data_id`)";

    $query = $ds->prepare($query, $value, md5($value));

    if (!@$ds->exec($query)) {
      throw new Exception("Exec failed");
    }

    return $ds->insertId();
  }
}
