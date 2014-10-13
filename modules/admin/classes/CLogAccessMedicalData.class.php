<?php 

/**
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

class CLogAccessMedicalData extends CMbMetaObject {
  public $access_id;

  public $user_id;
  public $datetime;
  public $group_id;

  public $_ref_user;
  public $_ref_group;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'log_access_medical_object';
    $spec->key   = 'access_id';
    $spec->loggable = false;
    return $spec;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]               = "ref class|CMediusers notNull";
    $props["datetime"]              = "dateTime notNull";
    $props["group_id"]              = "ref class|CGroups notNull";
    return $props;
  }

  /**
   * @return CMediusers
   */
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id", true);
  }

  /**
   * log into database an access
   *
   * @param int    $user_id      CMediusers_id
   * @param string $object_class Object_class
   * @param int    $object_id    Object_id
   * @param string $datetime     datetime
   * @param int    $group_id     group where access has been done
   *
   * @return bool the status of executed request.
   */
  static function logintoDb($user_id, $object_class, $object_id, $datetime, $group_id) {
    $object = new self();
    $ds = $object->getDS();
    $sql = "INSERT IGNORE INTO ".$object->_spec->table." (`access_id`, `user_id`, `datetime`, `object_class`, `object_id`, `group_id`)
    VALUES ('', '$user_id', '$datetime', '$object_class', '$object_id', '$group_id');";
    return $ds->exec($sql);
  }

  /**
   * logSejourAccess
   *
   * @param CSejour $sejour
   *
   * @return bool has the access been logged
   */
  static function logForSejour($sejour) {
    $group = $sejour->loadRefEtablissement();
    if (!CAppUI::conf("admin CLogAccessMedicalData enable_log_access", $group) || !$sejour->_id) {
      return false;
    }
    $user = CMediusers::get();
    $conf = CAppUI::conf("admin CLogAccessMedicalData round_datetime", $group);
    $datetime = CMbDT::dateTime();

    switch ($conf) {
      case '1m':  // minute
        $datetime = CMbDT::format($datetime, "%y-%m-%d %H:%M:00");
        break;

      case '10m': // 10 minutes
        $minute = CMbDT::format($datetime, "%M");
        $minute = str_pad(floor($minute / 10)*10, 2, 0, STR_PAD_RIGHT);
        $datetime = CMbDT::format($datetime, "%y-%m-%d %H:$minute:00");
        break;

      case '1d': // 1 day
        $datetime = CMbDT::format($datetime, "%y-%m-%d 00:00:00");
        break;

      default: // 1 hour
        $datetime = CMbDT::format($datetime, "%y-%m-%d %H:00:00");
        break;
    }

    return self::logintoDb($user->_id, $sejour->_class, $sejour->_id, $datetime, $group->_id);
  }

  /**
   * count the list of access for a sejour
   *
   * @param $sejour
   *
   * @return int
   */
  static function countListForSejour($sejour) {
    $log = new self();
    $where = array();
    $where["object_class"] = " = '$sejour->_class'";
    $where["object_id"] = " = '$sejour->_id' ";
    return $log->countList($where);
  }

  /**
   * load the list of access for a sejour
   *
   * @param $sejour
   * @param $page
   *
   * @return CLogAccessMedicalData[]
   */
  static function loadListForSejour($sejour, $page = 0, $step = 50) {
    $log = new self();
    $where = array();
    $where["object_class"] = " = '$sejour->_class'";
    $where["object_id"] = " = '$sejour->_id' ";
    $logs = $log->loadList($where, "datetime DESC", "$page, $step");

    return $logs;
  }


}