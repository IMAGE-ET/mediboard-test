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
 * User agent
 */
class CUserAgent extends CMbObject {
  static $browser_names = array(
    "Firefox",
    "Chrome",
    "IE",
    "Opera",
    "Opera Mini",
    "Safari",
    "Android",
    "Konqueror",
    "SeaMonkey",
    "Iceweasel",
  );

  static $platform_names = array(
    "WinNT",
    "Win2000",
    "WinXP",
    "WinVista",
    "Win7",
    "Win8",
    "Win8.1",
    "Linux",
    "MacOSX",
    "iOS",
    "Android",
    "ChromeOS",
    "unknown",
  );

  static $device_names = array(
    "PC",
    "Android",
    "iPhone",
    "iPad",
    "Nexus 4",
    "Blackberry",
    "general Mobile Device",
    "unknown",
  );

  static $device_makers = array(
    "Various",
    "Apple",
    "Samsung",
    "HTC",
    "LG",
    "SonyEricsson",
    "RIM",
    "Google",
    "Microsoft",
    "unknown",
  );

  public $user_agent_id;

  public $user_agent_string;

  public $browser_name;
  public $browser_version;

  public $platform_name;
  public $platform_version;

  public $device_name;
  public $device_maker;
  public $device_type; // Mobile Device, Mobile Phone, Desktop, Tablet
  public $pointing_method; // mouse, unknown, touchscreen

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "user_agent";
    $spec->key    = "user_agent_id";
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps(){
    $backProps = parent::getBackProps();
    $backProps["user_authentications"] = "CUserAuthentication user_agent_id";
    return $backProps;
  }
  
  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_agent_string"] = "str notNull";

    $props["browser_name"]      = "str";
    $props["browser_version"]   = "str";

    $props["platform_name"]     = "str";
    $props["platform_version"]  = "str";

    $props["device_name"]       = "str";
    $props["device_type"]       = "enum notNull list|desktop|mobile|tablet|unknown default|unknown";
    $props["device_maker"]      = "str";
    $props["pointing_method"]   = "enum notNull list|mouse|touchscreen|unknown default|unknown";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = "$this->browser_name $this->browser_version / $this->platform_name $this->platform_version";
  }

  /**
   * User agent detection
   *
   * @param string $ua_string UA string
   *
   * @return array
   */
  static function detect($ua_string) {
    $dir = __DIR__."/../../../classes/vendor/phpbrowscap/phpbrowscap/";
    include "$dir/Browscap.php";

    $detect = new \phpbrowscap\Browscap("$dir/cache.php");
    $detect->doAutoUpdate = false;
    $detect->iniFilename  = "browscap.ini";

    $user_agent = new self();
    $user_agent->user_agent_string = substr($ua_string, 0, 255);

    return $detect->getBrowser($ua_string, true);
  }

  /**
   * Create a User agent entry from a US string
   *
   * @param string $ua_string User agent string
   *
   * @return self
   */
  static function createFromUA($ua_string) {
    $user_agent = new self();
    $user_agent->user_agent_string = substr($ua_string, 0, 255);

    if (!$user_agent->loadMatchingObject()) {
      $browser = self::detect($ua_string);

      $user_agent->browser_name     = $browser["Browser"];
      $user_agent->browser_version  = $browser["Version"];

      $user_agent->platform_name    = $browser["Platform"];
      $user_agent->platform_version = $browser["Platform_Version"];

      $user_agent->device_name      = $browser["Device_Name"];
      $user_agent->device_maker     = $browser["Device_Maker"];
      $user_agent->pointing_method  = $browser["Device_Pointing_Method"];

      switch ($browser["Device_Type"]) {
        case "Mobile Device":
        case "Mobile Phone":
          $user_agent->device_type = "mobile";
          break;
        case "Desktop":
          $user_agent->device_type = "desktop";
          break;
        case "Tablet":
          $user_agent->device_type = "tablet";
          break;
        default:
          $user_agent->device_type = "unknown";
          break;
      }

      $user_agent->store();
    }

    return $user_agent;
  }
}
