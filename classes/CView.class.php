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
 * The VMC view class
 * Responsibilities :
 *  - view reflexion
 *  - view helpers
 */
class CView {
  /** @var stdClass Parameters values */
  public static $params;
  /** @var string[] Parameters properties */
  public static $props = array();
  /** @var bool View was slaved */
  public static $enslaved = false;
  /** @var bool View is currently slaved */
  public static $slavestate = false;
  /** @var string[] Protected param names */
  public static $protected_names = array("m", "a", "tab", "dialog", "raw", "ajax", "info", "enslave");

  /**
   * Get a REQUEST parameter
   *
   * @param string $name Name of the parameter
   * @param string $prop Property specification of the parameter
   *
   * @return any
   */
  static public function request($name, $prop) {
    return self::checkParam($name, $prop, CValue::request($name));
  }

  /**
   * Get a GET parameter
   *
   * @param string $name    Name of the parameter
   * @param string $prop    Property specification of the parameter
   * @param bool   $session Use session for retrieval if undefined
   *
   * @return any
   */
  static public function get($name, $prop, $session = false) {
    return self::checkParam($name, $prop, $session ? CValue::getOrSession($name) : CValue::get($name));
  }

  /**
   * Get a POST parameter
   *
   * @param string $name    Name of the parameter
   * @param string $prop    Property specification of the parameter
   * @param bool   $session Use session for retrieval if undefined
   *
   * @return any
   */
  static public function post($name, $prop, $session = false) {
    return self::checkParam($name, $prop, $session ? CValue::postOrSession($name) : CValue::post($name));
  }

  /**
   * Check a parameter
   *
   * @param string $name  Name of the parameter
   * @param string $prop  Property specification of the parameter
   * @param string $value Value of the paramter
   *
   * @return any
   */
  static public function checkParam($name, $prop, $value) {
    self::$params->$name =& $value;

    // Check the name
    if (in_array($name, self::$protected_names)) {
      $error = "View parameter '$name' is a protected name and should NOT be used.";
      trigger_error($error, E_USER_WARNING);
    }

    // Check duplicates
    if (array_key_exists($name, self::$props)) {
      $error = "View parameter '$name' is already in use.";
      trigger_error($error, E_USER_WARNING);
    }

    // Get Specification
    self::$props[$name] = $prop;
    $spec = CMbFieldSpecFact::getSpecWithClassName("stdClass", $name, $prop);

    // Defaults the value when available
    if (empty($value) && $spec->default) {
      $value = $spec->default;
    }

    // Could be null
    if ($value === "" || $value === null) {
      if (!$spec->notNull) {
        return $value;
      }
    }

    // Check the value
    if ($msg = $spec->checkPropertyValue(self::$params)) {
      $truncated = CMbString::truncate($value);
      $error = "View parameter '$name' with spec '$prop' has inproper value '$truncated': $msg";
      trigger_error($error, E_USER_WARNING);
    }

    return $value;
  }

  /**
   * Produce a regression check plan
   *
   * @param string[] $props Properties
   *
   * @return array Complete plan
   */
  static public function sampleCheckPlan($props) {
    return self::flatify((array)$props);

  }

  /**
   * Turn rercursive plan to flat plan
   *
   * @param string[] $props Parameters
   *
   * @return array
   */
  function flatify($props) {
    if (!count($props)) {
      return array(array());
    }

    // Spec for only first item
    $spec = null;
    foreach ($props as $_param => $_prop) {
      $spec = CMbFieldSpecFact::getSpecWithClassName("stdClass", $_param, $_prop);
      break;
    }

    // Shift this item and recurse plan
    array_shift($props);
    $subplan = self::flatify($props);

    // Complete with own values
    $plan = array();
    foreach ($subplan as $_subparts) {
      foreach ($spec->regressionSamples() as $_sample) {
        $parts = $_subparts;
        $parts[$spec->fieldName] = $_sample;
        $plan[] = $parts;
      }
    }
    return $plan;
  }

  /**
   * Close the parameter list definition and provides inspection information on info mode
   *
   * @return void
   */
  static public function checkin() {
    if (CValue::request("enslave")) {
      CView::enforceSlave();
    }

    if (!CValue::request("info")) {
      return;
    }

    // Dump properties on raw
    if (CValue::request("raw")) {
      echo json_encode(self::$props);
      CApp::rip();
    }

    // Show properties
    $smarty = new CSmartyDP("modules/system");
    $smarty->assign("props", self::$props);
    $smarty->display("view_info.tpl");
    CApp::rip();
  }

  /**
   * Enforce the current view to be rerouted on a slave SQL server if slave datasource is available
   *
   * @return void
   */
  static function enforceSlave() {
    // Enslaved views are supposably session stallers so close session preventively
    CSessionHandler::writeClose();

    // URL param enslave prevention
    if (CValue::request("enslave") === "0") {
      return;
    }

    // Test wether a slave datasource has been configured
    if (!CAppUI::conf("db slave dbhost")) {
      return;
    }

    // Check connection to the slave datasource or abandon std datasource rerouting
    if (!CSQLDataSource::get("slave", true)) {
      return;
    }

    self::$enslaved = true;
    self::$slavestate = true;
    self::rerouteStdDS();
  }

  /**
   * Enable the current view to forced to slave based on a enslaving ratio
   *
   * @return void
   */
  static function enableSlave() {
    // Enslaved views are supposably session stallers so close session preventively
    CSessionHandler::writeClose();

    if (rand(0, 100) < CAppUI::conf("enslaving_ratio")) {
      self::enforceSlave();
      return;
    }
  }

  /**
   * Disable current view reroute to slave SQL
   *
   * @return void
   */
  static function disableSlave() {
    if (!self::$slavestate) {
      return;
    }
    self::$slavestate = false;
    self::rerouteStdDS();
  }

  /**
   * Actual rerouting from std datasource to readonly slave datasource
   *
   * @return void
   */
  static function rerouteStdDS() {
    foreach (CStoredObject::$spec as $_spec) {
      if ($_spec->dsn === "std") {
        $_spec->init();
      }
    }
  }

}

CView::$params = new stdClass;