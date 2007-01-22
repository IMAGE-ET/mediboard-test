<?php /* $Id$ */

/**
* @package Mediboard
* @version $Revision$
* @author Thomas Despoix
*/

include_once("./includes/magic_quotes_gpc.php");

$dPconfig = array();
global $performance;
$performance = array();

if (!is_file("./includes/config.php")) {
  header("location: install/");
  die("Redirection vers l'assistant d'installation");
}

require_once("./includes/config_dist.php");
require_once("./includes/config.php");
require_once("./classes/sharedmemory.class.php");

// Check that the user has correctly set the root directory
is_file($dPconfig["root_dir"]."/includes/config.php") 
  or die("ERREUR FATALE: le repertoire racine est probablement mal configuré");

require_once("./includes/mb_functions.php");
require_once("./includes/main_functions.php");
require_once("./includes/errors.php");

// Start chrono
require_once("./classes/chrono.class.php");
$phpChrono = new Chronometer;
$phpChrono->start();

// PHP Configuration
ini_set("memory_limit", "64M");

// Load AppUI from session
require_once("./classes/ui.class.php");
require_once("./includes/session.php");

// Write the HTML headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");  // HTTP/1.0

require_once("./includes/db_connect.php");
require_once("./includes/autoload.php");

// Load default preferences if not logged in
if (!$AppUI->user_id) {
  $AppUI->loadPrefs(0);
}

// Don't output anything. Usefull for fileviewers, popup dialogs, ajax requests, etc.
$suppressHeaders = dPgetParam($_GET, "suppressHeaders");

// Output the charset header in case of an ajax request
$ajax = dPgetParam($_REQUEST, "ajax", false);

// Check if we are in the dialog mode
$dialog = dPgetParam( $_REQUEST, "dialog");

// check if the user is trying to log in
if(isset($_POST["login"])) {
  $username = dPgetParam($_POST   , "username", "");
  $password = dPgetParam($_POST   , "password", "");
  $md5      = dPgetParam($_POST   , "md5"     , 0);
  $redirect = dPgetParam($_REQUEST, "redirect", "");
  $ok = $AppUI->login($username, $password, $md5);
  if(!$ok) {
    @include_once("./locales/core.php");
    $AppUI->setMsg("Login Failed", UI_MSG_ERROR);
  }
  if($ok && $dialog) {
    $redirect = "m=system&a=login_ok&dialog=1";
  }
  $AppUI->redirect($redirect);
}

// Get the user preference
$uistyle = $AppUI->getPref("UISTYLE");

require_once( $AppUI->getSystemClass("smartydp"));

// clear out main url parameters
$m = "";
$a = "";
$u = "";
$g = "";

// load locale settings
$AppUI->setUserLocale();
require_once("./locales/core.php");
setlocale(LC_TIME, $AppUI->user_locale);

if (!$suppressHeaders || $ajax) {
  // output the character set header
  if (isset($locale_char_set)) {
    header("Content-type: text/html;charset=$locale_char_set");
  }
}

// check if we are logged in
if (!$AppUI->user_id) {
  
  $redirect = mbGetValueFromGet("logout") ?  "" : @$_SERVER["QUERY_STRING"]; 
  
  // Ajax login alert
  if ($ajax) {
    // Creation du Template
    $tplAjax = new CSmartyDP("modules/system");
    $tplAjax->assign("performance", $performance);
    $tplAjax->display("ajax_errors.tpl");

  } else {
    $smartyLogin = new CSmartyDP("style/$uistyle");
    $smartyLogin->assign("localeCharSet"        , $locale_char_set);
    $smartyLogin->assign("mediboardVersion"     , @$AppUI->getVersion());
    $smartyLogin->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico",1));
    $smartyLogin->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all",1));
    $smartyLogin->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all",1));
    $smartyLogin->assign("mediboardScript"      , mbLoadScripts(1));
    $smartyLogin->assign("demoVersion"          , $dPconfig["demo_version"]);
    $smartyLogin->assign("dialog"               , $dialog);
    $smartyLogin->assign("mb_version_major"     , $mb_version_major);
    $smartyLogin->assign("mb_version_minor"     , $mb_version_minor);
    $smartyLogin->assign("mb_version_patch"     , $mb_version_patch);
    $smartyLogin->assign("errorMessage"         , $AppUI->getMsg());
    $smartyLogin->assign("time"                 , time());
    $smartyLogin->assign("redirect"             , $redirect);
    $smartyLogin->assign("uistyle"              , $uistyle);
    $smartyLogin->assign("offline"              , false);
    $smartyLogin->display("login.tpl");
  }
  
  // destroy the current session and output login page
  session_unset();
  session_destroy();
  exit;
}

// set the module and action from the url
$m = $AppUI->checkFileName(mbGetValueFromGet("m", 0));
if(!$m) {
  // Select the default module
  $m = CPermModule::getVisibleModule();
  $pref_module = $AppUI->getPref("DEFMODULE");
  if($pref_module) {
    if(CPermModule::getViewModule(CModule::getInstalled($pref_module)->mod_id, PERM_READ)) {
      $m = $pref_module;
    }
  }
}

$a     = $AppUI->checkFileName(mbGetValueFromGet("a"     , "index"));
$u     = $AppUI->checkFileName(mbGetValueFromGet("u"     , ""));
$dosql = $AppUI->checkFileName(mbGetValueFromPost("dosql", ""));

$currentModule = CModule::getInstalled($m);
$listModules   = CPermModule::getVisibleModules();

// set the group in use, put the user group if not allowed
$g = mbGetAbsValueFromGetOrSession("g", $AppUI->user_group);
$indexGroup = new CGroups;
$indexGroup->load($g);

if(!$indexGroup->canRead()) {
  mbSetAbsValueToSession("g", $AppUI->user_group);
  $g = $AppUI->user_group;
}

// check overall module permissions
// these can be further modified by the included action files

$indexModule = CModule::getInstalled($m);
if(!$indexModule) {
  $AppUI->redirect("m=system&a=access_denied");
}
$canRead     = $indexModule->canRead();
$canEdit     = $indexModule->canEdit();
$canView     = $indexModule->canView();
$canAdmin    = $indexModule->canAdmin();
$canAuthor   = $canEdit;
$canDelete   = $canEdit;

// do some db work if dosql is set
if(isset($_REQUEST["dosql"])) {
  $mDo = isset($_REQUEST["m"]) ? $_REQUEST["m"] : $m;
  require("./modules/$mDo/$dosql.php");
}

ob_start();

$listActiveModules = CModule::getActive();

foreach($listActiveModules as $module) {
  require_once "./modules/".$module->mod_name."/index.php";
}
  
if (!$suppressHeaders) {
  
  // -- Code pour le HEADER --
  
  // Définition du CharSet
  if (!isset($locale_char_set)){
    $locale_char_set = "UTF-8";
  }
  //Liste des Etablissements
  $etablissements = CMediusers::loadEtablissements(PERM_EDIT);
  // Liste des Modules
  if (!$dialog) {
    //top navigation menu
    $iKey = 0;
    foreach ($listModules as $module) {
      $affModule[$iKey]["modName"]      = "$module->mod_name";
      $affModule[$iKey]["modNameCourt"] = $AppUI->_("module-$module->mod_name-court");
      $affModule[$iKey]["modNameLong"]  = $AppUI->_("module-$module->mod_name-long");
      $iKey++;
    }  
  }
  // Message
  $messages = new CMessage();
  $messages = $messages->loadPublications("present");
  
  // Titre et Image Module en cours
  $titleBlockData["name"]="module-$m-long";
  $titleBlockData["icon"]=dPshowImage( dPFindImage( "$m.png", $m ), "24", "24" );
  
  //Creation du Template
  $smartyHeader = new CSmartyDP();
  $smartyHeader->template_dir = "style/$uistyle/templates/";
  $smartyHeader->compile_dir  = "style/$uistyle/templates_c/";
  $smartyHeader->config_dir   = "style/$uistyle/configs/";
  $smartyHeader->cache_dir    = "style/$uistyle/cache/";
  if (!$dialog) {
    $smartyHeader->assign("affModule" , $affModule);
  }
  $smartyHeader->assign("offline"              , false);
  $smartyHeader->assign("baseUrl"              , null);
  $smartyHeader->assign("titleBlockData"       , $titleBlockData);
  $smartyHeader->assign("localeCharSet"        , $locale_char_set);
  $smartyHeader->assign("mediboardVersion"     , @$AppUI->getVersion());
  $smartyHeader->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico",1));
  $smartyHeader->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all",1));
  $smartyHeader->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all",1));
  $smartyHeader->assign("mediboardScript"      , mbLoadScripts(1));
  $smartyHeader->assign("dialog"               , $dialog);
  $smartyHeader->assign("messages"             , $messages);
  $smartyHeader->assign("uistyle"              , $uistyle);
  $smartyHeader->assign("AppUI"                , $AppUI);
  $smartyHeader->assign("Etablissements"       , $etablissements);
  $smartyHeader->assign("helpOnline"           , mbPortalLink($m, "Aide"));
  $smartyHeader->assign("suggestion"           , mbPortalLink("bugTracker", "Suggérer une amélioration"));
  $smartyHeader->assign("errorMessage"         , $AppUI->getMsg());
  $smartyHeader->display("header.tpl");
}

// -- Code pour les tabBox et Inclusion du fichier demandé --

$tab = $a == "index"  ? mbGetValueFromGetOrSession("tab", 1) : mbGetValueFromGet("tab");

if ($tab !== null) {
  $currentModule->showTabs();
} else {
  $currentModule->showAction();
}

$phpChrono->stop();

// Calcul des performances
if( !function_exists("memory_get_usage") ) {
  function memory_get_usage() {
    return "-";
  }
  
}
$performance["genere"]  = number_format($phpChrono->total, 3);
$performance["memoire"] = mbConvertDecaBinary(memory_get_usage());
$performance["objets"]  = $mbObjectCount;
$performance["cache"]   = $mbCacheObjectCount;
$performance["size"]    = mbConvertDecaBinary(ob_get_length());
  
// Inclusion du footer
if (!$suppressHeaders) {
  
  // Creation du Template
  $smartyFooter = new CSmartyDP("style/$uistyle");
  $smartyFooter->assign("offline"       , false);
  $smartyFooter->assign("debugMode"     , @$AppUI->user_prefs["INFOSYSTEM"]);
  $smartyFooter->assign("performance"   , $performance);
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

ob_end_flush();
require "./includes/access_log.php";

?>
