<?php
/**
 * Main URL Dispatcher
 *
 * PHP version 5.3.x+
 *
 * @category Dispatcher
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

require __DIR__."/classes/CMbPerformance.class.php";

CMbPerformance::start();

require __DIR__."/includes/compat.php";
require __DIR__."/includes/magic_quotes_gpc.php";

if (!is_file(__DIR__."/includes/config.php")) {
  header("Location: install/");
  die("Redirection vers l'assistant d'installation");
}

require __DIR__."/includes/config_all.php";

$rootName = basename($dPconfig["root_dir"]);

// Check that the user has correctly set the root directory
if (!is_file($dPconfig["root_dir"]."/includes/config.php")) {
  die("ERREUR FATALE: Le répertoire racine est probablement mal configuré");
}

// PHP Configuration
foreach ($dPconfig["php"] as $key => $value) {
  if ($value !== "") {
    ini_set($key, $value);
  }
}

date_default_timezone_set($dPconfig["timezone"]);

// Core classes and functions
require __DIR__."/includes/version.php";
require __DIR__."/includes/mb_functions.php";
require __DIR__."/includes/errors.php";
require __DIR__."/classes/SHM.class.php";
require __DIR__."/classes/CApp.class.php";
require __DIR__."/classes/CAppUI.class.php";
require __DIR__."/includes/autoload.php";

// Offline mode
if ($dPconfig["offline"]) {
  CApp::goOffline("maintenance");
}

// Migration mode
if ($dPconfig["migration"]["active"]) {
  header("Location: migration.php");
  exit;
}

// If offline period
if ($dPconfig["offline_time_start"] && $dPconfig["offline_time_end"]) {
  $time               = time();
  $offline_time_start = strtotime($dPconfig["offline_time_start"]);
  $offline_time_end   = strtotime($dPconfig["offline_time_end"]);

  if (($time >= $offline_time_start) && ($time <= $offline_time_end)) {
    CApp::goOffline("maintenance");
  }
}

// If baseBackup is running
if (!empty($dPconfig["base_backup_lockfile_path"])) {
  $backup_lockfile = realpath($dPconfig["base_backup_lockfile_path"]);
  if (file_exists($backup_lockfile)) {
    // File exists, we have to check lifetime
    $lock_mtime = filemtime($backup_lockfile);

    // Lock file is not dead
    if ((microtime(true) - $lock_mtime) <= 1800) {
      CApp::goOffline("db-backup");
    }

    unlink($backup_lockfile);
  }
}

// Include config in DB
if (CAppUI::conf("config_db")) {
  CMbConfig::loadValuesFromDB();
}

// Shutdown function
register_shutdown_function(array("CApp", "checkPeace"));

if (!@CSQLDataSource::get("std")) {
  CApp::goOffline("db-access");
}

CMbPerformance::mark("init");

require __DIR__."/includes/session.php";

CMbPerformance::mark("session");

// Start chrono (after session_start as it may be locked by another request)
CApp::$chrono = new Chronometer;
CApp::$chrono->main = true;
CApp::$chrono->start();

$do_login = false;

// Load default preferences if not logged in
if (!CAppUI::$instance->user_id) {
  CAppUI::loadPrefs();

  try {
    CApp::notify("UserAuthentication", true);
  }
  catch (CUserAuthenticationFailure $e) {
    CApp::rip();
  }
  catch (CUserAuthenticationSuccess $e) {
    CAppUI::$auth_info = $e;
    $do_login = true;
  }
}

// Update session lifetime
CSessionHandler::setUserDefinedLifetime();

/*
try {
  include __DIR__."/classes/CAuth.class.php";
  //CAuth::login();
}
catch (AuthenticationFailedException $e) {
  CAppUI::setMsg($e->getMessage());
}
*/

// If the user uses a token, his session should not be reset, but only redirected
$token_hash = CValue::get("token");
if ($token_hash) {
  $token = CViewAccessToken::getByHash($token_hash);
  // If the user is already logged in (in a normal session), keep his session, but use the params
  if (CAppUI::$instance->user_id && !CAppUI::$token_expiration) {
    if ($token->isValid() && CAppUI::$instance->user_id == $token->user_id) {
      $token->useIt();
      CAppUI::redirect($token->params);
      CApp::rip();
    }
  }
  else {
    $do_login = true;
  }
}

// We force the dialog view if in a token session
if (CAppUI::$token_expiration || $do_login) {
  $dialog = 1;
}

// Check ldap_guid or sining token
if (CValue::get("ldap_guid") || $do_login) {
  $_REQUEST["login"] = 1;
}

// check if the user is trying to log in
if (isset($_REQUEST["login"])) {
  $login_action = $_REQUEST["login"];

  // login with "login=user:password"
  if (strpos($login_action, ":") !== false) {
    list($_REQUEST["username"], $_REQUEST["password"]) = explode(":", $login_action, 2);
  }

  include __DIR__."/locales/core.php";
  if (null == $ok = CAppUI::login()) {
    CAppUI::$instance->user_id = null;

    // we delete the session in case the user was deactivated
    CAppUI::setMsg("Auth-failed", UI_MSG_ERROR);
  }

  if (isset($_SESSION['browser']['deprecated'])
      && $_SESSION['browser']['deprecated']
      && !CValue::get("password")
  ) { // If we are not connecting directly
    $tpl = new CSmartyDP("style/mediboard");
    $tpl->display("old_browser.tpl");
    CApp::rip();
  }

  // Login OK redirection for popup authentication
  $redirect = CValue::request("redirect");
  $dialog = CValue::request("dialog");
  parse_str($redirect, $parsed_redirect);
  if ($ok && $dialog && isset($parsed_redirect["login_info"])) {
    $redirect = "m=system&a=login_ok&dialog=1";
  }

  // Actual refirection
  if ($redirect) {
    CAppUI::redirect($redirect);
  }

  // Empty post data only if we login by POST (with the login page)
  if (isset($_POST["login"])) {
    CApp::emptyPostData();
  }
}

CMbPerformance::mark("auth");

// Default view
$index = "index";

// Don't output anything. Usefull for fileviewers, ajax requests, exports, etc.
$suppressHeaders = CValue::request("suppressHeaders");

// WSDL if often stated as final with no value (&wsdl) wrt client compat
$wsdl = CValue::request("wsdl");
if (isset($wsdl)) {
  $suppressHeaders = 1;
  $index = $wsdl;
  $wsdl = 1;
}

// Info output for view reflexion purposes
if ($info = CValue::request("info")) {
  $index = $info;
  $info = 1;
}

// Output the charset header in case of an ajax request
if ($ajax = CValue::request("ajax")) {
  $suppressHeaders = 1;
  $index = $ajax;
  $ajax = 1;
}

// Raw output for export purposes
if ($raw = CValue::request("raw")) {
  $suppressHeaders = 1;
  $index = $raw;
}

// Check if we are in the dialog mode
if ($dialog = CValue::request("dialog")) {
  $index = $dialog;
  $dialog = 1;
}
CAppUI::$dialog = &$dialog;

// clear out main url parameters
$m = $a = $u = $g = "";

CMbPerformance::mark("input");

// Locale
require __DIR__."/locales/core.php";

if (empty($locale_info["names"])) {
  $locale_info["names"] = array();
}
setlocale(LC_TIME, $locale_info["names"]);

if (empty($locale_info["charset"])) {
  $locale_info["charset"] = "UTF-8";
}

// We don't use mb_internal_encoding as it may be redefined by libs
CApp::$encoding = $locale_info["charset"];

// Character set
if (!$suppressHeaders || $ajax) {
  header("Content-type: text/html;charset=".CApp::$encoding);
}

CMbPerformance::mark("locales");

// HTTP headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
header("Cache-Control: no-cache, no-store, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");  // HTTP/1.0
header("X-UA-Compatible: IE=edge"); // Force IE document mode

// Show errors to admin
ini_set("display_errors", CAppUI::pref("INFOSYSTEM"));

CMbPerformance::mark("headers");

$user = new CMediusers();
if ($user->isInstalled()) {
  $user->load(CAppUI::$instance->user_id);
  $user->getBasicInfo();
  CAppUI::$user = $user;
  CAppUI::$instance->_ref_user =& CAppUI::$user;

  CApp::$is_robot = CAppUI::$user->isRobot();

  // Offline mode for non-admins
  if ($dPconfig["offline_non_admin"] && CAppUI::$user->_id != 0 && !CAppUI::$user->isAdmin()) {
    CApp::goOffline("maintenance");
  }
}

CMbPerformance::mark("user");

// Load DB-stored configuration schema
$configurations = glob(__DIR__."/modules/*/configuration.php");
foreach ($configurations as $_configuration) {
  include $_configuration;
}

CMbPerformance::mark("config");

// Init output filter
CHTMLResourceLoader::initOutput(CValue::get("_aio"));

CApp::notify("BeforeMain");

// Check if the mobile feature is available and if the user agent is a mobile
$enable_mobile_ui = CAppUI::pref("MobileUI") || !CAppUI::$user->_id;
if (is_file(__DIR__."/mobile/main.php") && !empty($_SESSION["browser"]["mobile"]) && $enable_mobile_ui) {
  CAppUI::$mobile = true;
  include __DIR__."/mobile/main.php";
}
else {
  include __DIR__."/includes/main.php";
}

CView::disableSlave();

CApp::notify("AfterMain");

// Send timing data in HTTP header
CMbPerformance::end();

CMbPerformance::writeHeader();

// Output HTML
$aio_options = array(
  "ignore_scripts" => CValue::get("_aio_ignore_scripts")
);
CHTMLResourceLoader::output($aio_options);

CApp::rip();
