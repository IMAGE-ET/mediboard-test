<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require("./includes/magic_quotes_gpc.php");

$performance = array();

if (!is_file("./includes/config.php")) {
  header("Location: install/");
  die("Redirection vers l'assistant d'installation");
}

require("./includes/config_dist.php");
require("./includes/config.php");
require("./includes/version.php");
require("./includes/compat.php");
require("./classes/sharedmemory.class.php");

// PHP Configuration
foreach($dPconfig["php"] as $key => $value) {
  if ($value === "") continue;
  ini_set($key, $value);
}

if ($dPconfig["offline"]) {
  header("Location: offline.php");
  die("Le système est actuellement en cours de maintenance");
}

// Check that the user has correctly set the root directory
is_file ($dPconfig["root_dir"]."/includes/config.php") 
  or die("ERREUR FATALE: Le répertoire racine est probablement mal configuré");

require("./includes/mb_functions.php");
require("./includes/errors.php");

date_default_timezone_set($dPconfig["timezone"]);

// Start chrono
require("./classes/chrono.class.php");
$phpChrono = new Chronometer;
$phpChrono->start();

// Load AppUI from session
require("./classes/ui.class.php");
require("./includes/session.php");

// Register shutdown
register_shutdown_function(array("CApp", "checkPeace"));

require("./classes/sqlDataSource.class.php");
require("./classes/mysqlDataSource.class.php");

if(!CSQLDataSource::get("std")) {
  header("Location: offline.php?reason=bdd");
  die("La base de données n'est pas connectée");
}

// Write the HTML headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");  // HTTP/1.0

require("./includes/autoload.php");

// Load default preferences if not logged in
if (!CAppUI::$instance->user_id) {
  CAppUI::loadPrefs();
}

// Don't output anything. Usefull for fileviewers, popup dialogs, ajax requests, etc.
$suppressHeaders = mbGetValueFromRequest("suppressHeaders");

// Output the charset header in case of an ajax request
$ajax = mbGetValueFromRequest("ajax", false);

// Check if we are in the dialog mode
$dialog = mbGetValueFromRequest("dialog");

// check if the user is trying to log in
if (isset($_REQUEST["login"])) {
  require("./locales/core.php");
  $redirect = mbGetValueFromRequest("redirect");

  $ok = CAppUI::login();
  if (!$ok) {
    CAppUI::setMsg("Auth-failed", UI_MSG_ERROR);
  }
  
  if ($ok && $dialog && !isset($_REQUEST["no_login_info"])) {
    $redirect = "m=system&a=login_ok&dialog=1";
  }

  if($redirect) {
    CAppUI::redirect($redirect);
  }
}

// Get the user preference
$uistyle = CAppUI::pref("UISTYLE");
if (!is_dir("style/$uistyle")) {
  $uistyle = "mediboard";
}

CAppUI::requireSystemClass("smartydp");

// clear out main url parameters
$m = $a = $u = $g = "";

// load locale settings
require("./locales/core.php");

if (empty($locale_info["names"])){
  $locale_info["names"] = array();
}
setlocale(LC_TIME, $locale_info["names"]);

if (empty($locale_info["charset"])){
  $locale_info["charset"] = "UTF-8";
}

// output the character set header
if (!$suppressHeaders || $ajax) {
  header("Content-type: text/html;charset=".$locale_info["charset"]);
}

// check if we are logged in
if (!CAppUI::$instance->user_id) {
  $redirect = mbGetValueFromGet("logout") ?  "" : @$_SERVER["QUERY_STRING"]; 
  $_SESSION["locked"] = null;
  
  // Ajax login alert
  if ($ajax) {
    $tplAjax = new CSmartyDP("modules/system");
    $tplAjax->assign("performance", $performance);
    $tplAjax->display("ajax_errors.tpl");
  } 
  else {
    $smartyLogin = new CSmartyDP("style/$uistyle");
    $smartyLogin->assign("localeInfo"           , $locale_info);
    $smartyLogin->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico", true));
    $smartyLogin->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all", true));
    $smartyLogin->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all", true));
    $smartyLogin->assign("mediboardScript"      , mbLoadScripts(true));
    $smartyLogin->assign("errorMessage"         , CAppUI::getMsg());
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
ini_set("display_errors", CAppUI::pref("INFOSYSTEM"));

$user = new CMediusers();
if ($user->isInstalled()) {
  $user->load(CAppUI::$instance->user_id);
  CAppUI::$instance->_ref_user = $user;
}
CAppUI::$instance->_ref_user->getBasicInfo();

// Set the module and action from the url
if (null == $m = CAppUI::checkFileName(mbGetValueFromGet("m", 0))) {
  $m = CPermModule::getFirstVisibleModule();
  if ($pref_module = CAppUI::pref("DEFMODULE")) {
    if (CPermModule::getViewModule(CModule::getInstalled($pref_module)->mod_id, PERM_READ)) {
      $m = $pref_module;
    }
  }
}

// Still no target module
if (null == $m) {
  CAppUI::redirect("m=system&a=access_denied");
}

if (null == $currentModule = CModule::getInstalled($m)) {
  CAppUI::redirect("m=system&a=module_missing&module=$m");
}

// Get current module permissions
// these can be further modified by the included action files
$can = $currentModule->canDo();

$a     = CAppUI::checkFileName(mbGetValueFromGet("a"     , "index"));
$u     = CAppUI::checkFileName(mbGetValueFromGet("u"     , ""));
$dosql = CAppUI::checkFileName(mbGetValueFromPost("dosql", ""));

$tab = $a == "index" ? 
  mbGetValueFromGetOrSession("tab", 1) : 
  mbGetValueFromGet("tab");

// Check whether the password is strong enough
if (CAppUI::$instance->weak_password && 
    !CAppUI::$instance->user_remote && 
    !($m == "admin" && $tab == "chpwd")) {
  CAppUI::redirect("m=admin&tab=chpwd&forceChange=1");
}

// set the group in use, put the user group if not allowed
$g = mbGetAbsValueFromGetOrSession("g", CAppUI::$instance->user_group);
$indexGroup = new CGroups;
$indexGroup->load($g);
if ($indexGroup->_id) {
	if (!$indexGroup->canRead()) {
    $g = CAppUI::$instance->user_group;
	  mbSetAbsValueToSession("g", $g);
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
  require("./modules/$module->mod_name/index.php");
}

$currentModule->addConfigureTab();

if (!$a || $a === "index")
  $tab = $currentModule->getValidTab($tab);

if (!$suppressHeaders) {
  // Liste des Etablissements
  $etablissements = CMediusers::loadEtablissements(PERM_EDIT);

  // Messages
  $messages = new CMessage();
  $messages = $messages->loadPublications("present", $m);
  
  // Mails
  $mail = new CMbMail();
  $mails = $mail->loadVisibleList();
  
  // Load the SVN latest update info
  $svnStatus = null;
  if (CAppUI::pref("showLastUpdate") && is_readable("./tmp/svnstatus.txt")) {
    $svnInfo = file("./tmp/svnstatus.txt");
    $svnStatus = array( 
      "revision" => explode(": ", $svnInfo[0]),
      "date"     => explode(": ", $svnInfo[1]),
    );    

    $svnStatus["revision"] = $svnStatus["revision"][1];
    $svnStatus["date"]     = $svnStatus["date"][1];
    $svnStatus["relative"] = CMbDate::relative($svnStatus["date"]);
  }
  
  // Creation du Template
  $smartyHeader = new CSmartyDP();
  $smartyHeader->template_dir = "style/$uistyle/templates/";
  $smartyHeader->compile_dir  = "style/$uistyle/templates_c/";
  $smartyHeader->config_dir   = "style/$uistyle/configs/";
  $smartyHeader->cache_dir    = "style/$uistyle/cache/";
  
  $smartyHeader->assign("offline"              , false);
  $smartyHeader->assign("nodebug"              , true);
  $smartyHeader->assign("configOffline"        , null);
  $smartyHeader->assign("localeInfo"           , $locale_info);
  $smartyHeader->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico", true));
  $smartyHeader->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all", true));
  $smartyHeader->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all", true));
  $smartyHeader->assign("mediboardScript"      , mbLoadScripts(true));
  $smartyHeader->assign("dialog"               , $dialog);
  $smartyHeader->assign("messages"             , $messages);
  $smartyHeader->assign("mails"                , $mails);
  $smartyHeader->assign("uistyle"              , $uistyle);
  $smartyHeader->assign("browser"              , $browser);
  $smartyHeader->assign("errorMessage"         , CAppUI::getMsg());
  $smartyHeader->assign("Etablissements"       , $etablissements);
  $smartyHeader->assign("svnStatus"            , $svnStatus);
  $smartyHeader->assign("portal"               , array (
    "help" => mbPortalURL($m, $tab),
    "tracker" => mbPortalURL("tracker"),
  ));
  
  $smartyHeader->display("header.tpl");
}

// tabBox et inclusion du fichier demandé
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

foreach (CSQLDataSource::$dataSources as $dsn => $ds) {
  if (!$ds) continue;
  
  $chrono = $ds->chrono;
  $performance["dataSources"][$dsn] = array(
    "count" => $chrono->nbSteps,
    "time" => $chrono->total,
  );
}

// Inclusion du footer
if (!$suppressHeaders) {
  $smartyFooter = new CSmartyDP("style/$uistyle");
  $smartyFooter->assign("offline"       , false);
  $smartyFooter->assign("debugMode"     , CAppUI::pref("INFOSYSTEM"));
  $smartyFooter->assign("performance"   , $performance);
  $smartyFooter->assign("userIP"        , $_SERVER["REMOTE_ADDR"]);
  $smartyFooter->assign("errorMessage"  , CAppUI::getMsg());
  $smartyFooter->display("footer.tpl");
}

// Ajax performance
if ($ajax) {
  $tplAjax = new CSmartyDP("modules/system");
  $tplAjax->assign("performance", $performance);
  $tplAjax->display("ajax_errors.tpl");
}

require("./includes/access_log.php");
ob_end_flush();

CApp::rip();
