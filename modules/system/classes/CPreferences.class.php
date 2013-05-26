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
 * User preferences
 */
class CPreferences extends CMbObject {
  static $modules = array();

  public $pref_id;

  public $user_id;
  public $key;
  public $value;

  /**
   * Load preferences files from each module
   *
   * @return void
   */
  static function loadModules() {
    foreach (glob("./modules/*/preferences.php") as $file) {
      include_once $file;
    }
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "user_preferences";
    $spec->key   = "pref_id";
    $spec->uniques["uniques"] = array("user_id", "key");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"] = "ref class|CUser";
    $props["key"]     = "str notNull maxLength|40";
    $props["value"]   = "str";
    return $props;
  }

  /**
   * Load user preferences as an associative array
   *
   * @param null $user_id The user to load the preferences from
   *
   * @return array
   */
  static function get($user_id = null) {
    $where["user_id"] = "IS NULL";
    if ($user_id) {
      $where["user_id"] = "= '$user_id'";
      $where["value"  ] = "IS NOT NULL";
    }

    $preferences = array();
    $pref = new self;

    /** @var self[] $list */
    $list = $pref->loadList($where);

    foreach ($list as $_pref) {
      $preferences[$_pref->key] = $_pref->value;
    }

    return $preferences;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    $this->loadRefUser();
  }

  /**
   * Load ref user
   *
   * @return CUser
   */
  function loadRefUser(){
    return $this->loadFwdRef("user_id", true);
  }
}
