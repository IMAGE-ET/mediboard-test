<?php
/**
 * Main URL Dispatcher
 *
 * PHP version 5.1.x+
 *
 * @category Dispatcher
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

require "./includes/compat.php";
require "./includes/magic_quotes_gpc.php";

if (!is_file("./includes/config.php")) {
  header("Location: install/");
  die("Redirection vers l'assistant d'installation");
}

require "./includes/config_all.php";

$rootName = basename($dPconfig["root_dir"]);

require "./includes/version.php";

// PHP Configuration
foreach ($dPconfig["php"] as $key => $value) {
  if ($value !== "") {
    ini_set($key, $value);
  }
}

if ($dPconfig["offline"]) {
  header("Location: offline.php");
  die("Le système est actuellement en cours de maintenance");
}

if ($dPconfig["migration"]["active"]) {
  header("Location: migration.php");
  exit;
}

// Check that the user has correctly set the root directory
if (!is_file($dPconfig["root_dir"]."/includes/config.php")) {
  die("ERREUR FATALE: Le répertoire racine est probablement mal configuré");
}

date_default_timezone_set($dPconfig["timezone"]);

// Cord classes and functions
require "./includes/mb_functions.php";
require "./includes/errors.php";
require "./classes/SHM.class.php";
require "./classes/CApp.class.php";
require "./classes/CAppUI.class.php";
require "./includes/autoload.php";

// Shutdown function
register_shutdown_function(array("CApp", "checkPeace"));

if (!@CSQLDataSource::get("std")) {
  header("Location: offline.php?reason=bdd");
  die("La base de données n'est pas connectée");
}

require "./classes/CSessionHandler.class.php"; // Explicit include before session.php
require "./includes/session.php";

// Start chrono (after session_start as it may be locked by another request)
CApp::$chrono = new Chronometer;
CApp::$chrono->main = true;
CApp::$chrono->start();

// Load default preferences if not logged in
if (!CAppUI::$instance->user_id) {
  CAppUI::loadPrefs();
}

// Default view
$index = "index";

// Don't output anything. Usefull for fileviewers, ajax requests, exports, etc.
$suppressHeaders = CValue::request("suppressHeaders");
$token_hash = CValue::get("token");

// WSDL if often stated as final with no value (&wsdl) wrt client compat
$wsdl = CValue::request("wsdl");
if (isset($wsdl)) {
  $suppressHeaders = 1;
  $index = $wsdl;
  $wsdl = 1;
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

// If the user uses a token, his session should not be reset, but only redirected
$do_login = false;
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

  include "./locales/core.php";
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

  $redirect = CValue::request("redirect");
  parse_str($redirect, $parsed_redirect);
  if ($ok && $dialog && isset($parsed_redirect["login_info"])) {
    $redirect = "m=system&a=login_ok&dialog=1";
  }

  if ($redirect) {
    CAppUI::redirect($redirect);
  }

  // Empty post data only if we login by POST (with the login page)
  if (isset($_POST["login"])) {
    CApp::emptyPostData();
  }
}

// clear out main url parameters
$m = $a = $u = $g = "";

// Locale
require "./locales/core.php";

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

// HTTP headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
header("Cache-Control: no-cache, no-store, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");  // HTTP/1.0
$ie_mode = CAppUI::conf("browser_enable_ie9");
$map = array(
  0 => 8,
  1 => 9,
  2 => "edge",
);
header("X-UA-Compatible: IE=".CValue::read($map, $ie_mode)); // Force IE document mode
//header("X-UA-Compatible: IE=8;chrome=1"); // To use the ChromeFrame plugin in IE

// Show errors to admin
ini_set("display_errors", CAppUI::pref("INFOSYSTEM"));

$user = new CMediusers();
if ($user->isInstalled()) {
  $user->load(CAppUI::$instance->user_id);
  $user->getBasicInfo();
  CAppUI::$user = $user;
  CAppUI::$instance->_ref_user =& CAppUI::$user;
}

// Load DB-stored configuration schema
$configurations = glob("./modules/*/configuration.php");
foreach ($configurations as $_configuration) {
  include $_configuration;
}

// Init output filter
CHTMLResourceLoader::initOutput(CValue::get("_aio"));

CApp::notify("BeforeMain");


// Check if the mobile feature is available and if the user agent is a mobile
$enable_mobile_ui = CAppUI::pref("MobileUI") || !CAppUI::$user->_id;
if (is_file("./mobile/main.php") && !empty($_SESSION["browser"]["mobile"]) && $enable_mobile_ui) {
  CAppUI::$mobile = true;
  include "./mobile/main.php";
}
else {
  include "./includes/main.php";
}

CApp::notify("AfterMain");

require "./includes/access_log.php";

// Output HTML
CHTMLResourceLoader::output();

CApp::rip();
