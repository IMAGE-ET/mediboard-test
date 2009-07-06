<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

include_once("./includes/magic_quotes_gpc.php");

$dPconfig = array();
$performance = array();

if (!is_file("./includes/config.php")) {
  header("location: install/");
  die("Redirection vers l'assistant d'installation");
}

// PHP Configuration
ini_set("memory_limit", "128M");
if(function_exists("date_default_timezone_set")) {
  date_default_timezone_set("Europe/Paris");
}

require_once("./includes/config_dist.php");
require_once("./includes/config.php");
require_once("./includes/version.php");
require_once("./classes/sharedmemory.class.php");

if ($dPconfig["offline"]) {
  header("Location: offline.php");
  die("Le système est actuellement en cours de maintenance");
}

// Check that the user has correctly set the root directory
is_file ($dPconfig["root_dir"]."/includes/config.php") 
  or die("ERREUR FATALE: Le répertoire racine est probablement mal configuré");

require_once("./includes/mb_functions.php");
require_once("./includes/compat.php");
require_once("./includes/errors.php");

// Start chrono
require_once("./classes/chrono.class.php");
$phpChrono = new Chronometer;
$phpChrono->start();

// Load AppUI from session
require_once("./classes/ui.class.php");
require_once("./includes/session.php");

// Register shutdown
register_shutdown_function(array("CApp", "checkPeace"));

// Write the HTML headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");  // HTTP/1.0

require_once("./classes/sqlDataSource.class.php");
require_once("./classes/mysqlDataSource.class.php");  	

require_once("./includes/autoload.php");

// Load default preferences if not logged in
if (!$AppUI->user_id) {
  $AppUI->loadPrefs(0);
}

// Don't output anything. Usefull for fileviewers, popup dialogs, ajax requests, etc.
$suppressHeaders = mbGetValueFromRequest("suppressHeaders");

// Output the charset header in case of an ajax request
$ajax = mbGetValueFromRequest("ajax", false);

// Check if we are in the dialog mode
$dialog = mbGetValueFromRequest("dialog");

// check if the user is trying to log in
if (isset($_REQUEST["login"])) {
  include_once("./locales/core.php");
  $redirect = mbGetValueFromRequest("redirect");

  $ok = $AppUI->login();
  if (!$ok) {
    $AppUI->setMsg("Auth-failed", UI_MSG_ERROR);
  }
  
  if ($ok && $dialog) {
    $redirect = "m=system&a=login_ok&dialog=1";
  }

  if($redirect) {
    $AppUI->redirect($redirect);
  }
}

// Get the user preference
$uistyle = $AppUI->user_prefs["UISTYLE"];

CAppUI::requireSystemClass("smartydp");

// clear out main url parameters
$m = "";
$a = "";
$u = "";
$g = "";

// load locale settings
require_once("./locales/core.php");
if (!isset($locale_names)){
  $locale_names = array();
}

setlocale(LC_TIME, $locale_names);

if (!isset($locale_char_set)){
  $locale_char_set = "UTF-8";
}

// output the character set header
if (!$suppressHeaders || $ajax) {
  header("Content-type: text/html;charset=$locale_char_set");
}

// check if we are logged in
if (!$AppUI->user_id) {
  $redirect = mbGetValueFromGet("logout") ?  "" : @$_SERVER["QUERY_STRING"]; 
  $_SESSION["locked"] = null;
  
  // Ajax login alert
  if ($ajax) {
    // Creation du Template
    $tplAjax = new CSmartyDP("modules/system");
    $tplAjax->assign("performance", $performance);
    $tplAjax->display("ajax_errors.tpl");
  } 
  else {
    $smartyLogin = new CSmartyDP("style/$uistyle");
    $smartyLogin->assign("localeCharSet"        , $locale_char_set);
    $smartyLogin->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico", true));
    $smartyLogin->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all", true));
    $smartyLogin->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all", true));
    $smartyLogin->assign("mediboardScript"      , mbLoadScripts(true));
    $smartyLogin->assign("demoVersion"          , $dPconfig["demo_version"]);
    $smartyLogin->assign("errorMessage"         , $AppUI->getMsg());
    $smartyLogin->assign("time"                 , time());
    $smartyLogin->assign("redirect"             , $redirect);
    $smartyLogin->assign("uistyle"              , $uistyle);
    $smartyLogin->assign("browser"              , $browser);
    $smartyLogin->assign("nodebug"              , true);
    $smartyLogin->assign("offline"              , false);
    $smartyLogin->display("login.tpl");
  }
  
  // Destroy the current session and output login page
  session_unset();
  session_destroy();
  CApp::rip();
}

// Show errors to admin
ini_set("display_errors", $AppUI->user_prefs["INFOSYSTEM"]);
  
$user = new CMediusers();
if ($user->isInstalled()) {
  $user->load($AppUI->user_id);
  $AppUI->_ref_user = $user;
}

// Set the module and action from the url
if (null == $m = $AppUI->checkFileName(mbGetValueFromGet("m", 0))) {
  $m = CPermModule::getFirstVisibleModule();
  if ($pref_module = $AppUI->user_prefs["DEFMODULE"]) {
    if (CPermModule::getViewModule(CModule::getInstalled($pref_module)->mod_id, PERM_READ)) {
      $m = $pref_module;
    }
  }
}
    
// Still no target module
if (null == $m) {
  $AppUI->redirect("m=system&a=access_denied");
}

if (null == $currentModule = CModule::getInstalled($m)) {
  $AppUI->redirect("m=system&a=module_missing&module=$m");
}

// Get current module permissions
// these can be further modified by the included action files
$can = $currentModule->canDo();

$a     = $AppUI->checkFileName(mbGetValueFromGet("a"     , "index"));
$u     = $AppUI->checkFileName(mbGetValueFromGet("u"     , ""));
$dosql = $AppUI->checkFileName(mbGetValueFromPost("dosql", ""));

$tab = $a == "index"  ? 
  mbGetValueFromGetOrSession("tab", 1) : 
  mbGetValueFromGet("tab");

// Check whether the password is strong enough
if ($AppUI->weak_password && 
    !$AppUI->user_remote && 
    !($m == "admin" && $tab == "chpwd")) {
  $AppUI->redirect("m=admin&tab=chpwd&forceChange=1");
}

// set the group in use, put the user group if not allowed
$g = mbGetAbsValueFromGetOrSession("g", $AppUI->user_group);
$indexGroup = new CGroups;
$indexGroup->load($g);
if ($indexGroup->_id) {
	if (!$indexGroup->canRead()) {
	  mbSetAbsValueToSession("g", $AppUI->user_group);
	  $g = $AppUI->user_group;
	}
}

// do some db work if dosql is set
if ($dosql) {
  $mDo = mbGetValueFromPost("m", $m);
  require("./modules/$mDo/$dosql.php");
}

ob_start();

// Feed modules with tabs
foreach (CModule::getActive() as $module) {
  require_once "./modules/$module->mod_name/index.php";
}

if (!$suppressHeaders) {
  // Liste des Etablissements
  $etablissements = CMediusers::loadEtablissements(PERM_EDIT);

  // Messages
  $messages = new CMessage();
  $messages = $messages->loadPublications("present", $m);
  
  // Mails
  $mail = new CMbMail();
  $mails = $mail->loadVisibleList();
  
  // Creation du Template
  $smartyHeader = new CSmartyDP();
  $smartyHeader->template_dir = "style/$uistyle/templates/";
  $smartyHeader->compile_dir  = "style/$uistyle/templates_c/";
  $smartyHeader->config_dir   = "style/$uistyle/configs/";
  $smartyHeader->cache_dir    = "style/$uistyle/cache/";
  
  $smartyHeader->assign("offline"              , false);
  $smartyHeader->assign("nodebug"              , true);
  $smartyHeader->assign("configOffline"        , null);
  $smartyHeader->assign("localeCharSet"        , $locale_char_set);
  $smartyHeader->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico", true));
  $smartyHeader->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all", true));
  $smartyHeader->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all", true));
  $smartyHeader->assign("mediboardScript"      , mbLoadScripts(true));
  $smartyHeader->assign("dialog"               , $dialog);
  $smartyHeader->assign("messages"             , $messages);
  $smartyHeader->assign("mails"                , $mails);
  $smartyHeader->assign("uistyle"              , $uistyle);
  $smartyHeader->assign("browser"              , $browser);
  $smartyHeader->assign("errorMessage"         , $AppUI->getMsg());
  $smartyHeader->assign("Etablissements"       , $etablissements);
  $smartyHeader->assign("portal"               , array (
    "help" => mbPortalURL($m, $tab),
    "tracker" => mbPortalURL("tracker"),
  ));
  
  $smartyHeader->display("header.tpl");
}

// -- Code pour les tabBox et Inclusion du fichier demandé --

if ($tab !== null) {
  $currentModule->showTabs();
} else {
  $currentModule->showAction();
}

$phpChrono->stop();

$performance["genere"]         = number_format($phpChrono->total, 3);
$performance["memoire"]        = mbConvertDecaBinary(memory_get_usage());
$performance["objets"]         = CMbObject::$objectCount;
$performance["cachableCount"]  = array_sum(CMbObject::$cachableCounts);
$performance["cachableCounts"] = CMbObject::$cachableCounts;

$performance["size"] = mbConvertDecaBinary(ob_get_length());
$performance["ccam"] = array (
  "cacheCount" => class_exists('CCodeCCAM') ? CCodeCCAM::$cacheCount : 0,
  "useCount"   => class_exists('CCodeCCAM') ? CCodeCCAM::$useCount : 0
);

foreach (CSQLDataSource::$dataSources as $dsn => $dataSource) {
  if (!$dataSource) {
    continue;
  }
  
  $chrono = $dataSource->chrono;
  $performance["dataSources"][$dataSource->dsn] = array(
    "count" => $chrono->nbSteps,
    "time" => $chrono->total,
  );
}

// Inclusion du footer
if (!$suppressHeaders) {
  
  // Creation du Template
  $smartyFooter = new CSmartyDP("style/$uistyle");
  $smartyFooter->assign("offline"       , false);
  $smartyFooter->assign("debugMode"     , @$AppUI->user_prefs["INFOSYSTEM"]);
  $smartyFooter->assign("performance"   , $performance);
  $smartyFooter->assign("userIP"        , $_SERVER["REMOTE_ADDR"]);
  $smartyFooter->assign("errorMessage"  , $AppUI->getMsg());
  $smartyFooter->assign("demoVersion"   , $dPconfig["demo_version"]);
  $smartyFooter->display("footer.tpl");
}

// Ajax performance
if ($ajax) {
  // Creation du Template
  $tplAjax = new CSmartyDP("modules/system");
  $tplAjax->assign("performance", $performance);
  $tplAjax->display("ajax_errors.tpl");
}

require "./includes/access_log.php";
ob_end_flush();

CApp::rip();

?>