<?php /* $Id$ */

/**
* @package Mediboard
* @version $Revision$
* @author Thomas Despoix
*/

$dPconfig = array();

if(!is_file("./includes/config.php")) {
  header("location: install/");
  die("Redirection vers l'assistant d'installation");
}

require_once("./classes/ui.class.php");
require_once("./includes/config.php");

// Check that the user has correctly set the root directory
is_file($dPconfig["root_dir"]."/includes/config.php") 
  or die("ERREUR FATALE: le repertoire racine est probablement mal configuré");

require_once("./includes/main_functions.php");
require_once("./includes/errors.php");

// PHP Configuration
ini_set("memory_limit", "64M");
ini_set("magic_quotes_gpc", 1);

// manage the session variable(s)
session_name("dotproject");
if(get_cfg_var("session.auto_start") > 0) {
	session_write_close();
}
session_start();
session_register("AppUI");
  
// write the HTML headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");	// Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// always modified
header("Cache-Control: no-cache, must-revalidate");	// HTTP/1.1
header("Pragma: no-cache");	// HTTP/1.0

// check if session has previously been initialised
if(!isset($_SESSION["AppUI"]) || isset($_GET["logout"])) {
  $_SESSION["AppUI"] = new CAppUI();
}

$AppUI =& $_SESSION["AppUI"];
$AppUI->setConfig($dPconfig);
$AppUI->checkStyle();
$AppUI->getAllClasses();

$phpChrono = new Chronometer;
$phpChrono->start();

// load default preferences if not logged in
if($AppUI->doLogin()) {
    $AppUI->loadPrefs(0);
}

// check if the user is trying to log in
if(isset($_POST["login"])) {
	$username = dPgetParam($_POST, "username", "");
	$password = dPgetParam($_POST, "password", "");
	$redirect = dPgetParam($_REQUEST, "redirect", "");
	$ok = $AppUI->login($username, $password);
	if(!$ok) {
		@include_once("./locales/core.php");
		$AppUI->setMsg("Login Failed", UI_MSG_ERROR);
	}
	$AppUI->redirect("$redirect");
}

// Get the user preference
$uistyle = $AppUI->getPref("UISTYLE");

// clear out main url parameters
$m = "";
$a = "";
$u = "";
$g = "";

// check if we are logged in
if($AppUI->doLogin()) {
  $AppUI->setUserLocale();
	// load basic locale settings
	@include_once("./locales/$AppUI->user_locale/locales.php");
	@include_once("./locales/core.php");
	setlocale(LC_TIME, $AppUI->user_locale);

	$redirect = @$_SERVER["QUERY_STRING"];
	if(strpos($redirect, "logout") !== false) {
		$redirect = "";
	}

	if(isset($locale_char_set)) {
		header("Content-type: text/html;charset=$locale_char_set");
	}

	require "./style/$uistyle/login.php";
	// destroy the current session and output login page
	session_unset();
	session_destroy();
	exit;
}

// set the module and action from the url
$m     = $AppUI->checkFileName(mbGetValueFromGet("m"     , CPermModule::getVisibleModule()));
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

// load locale settings
@include_once("./locales/$AppUI->user_locale/locales.php");
@include_once("./locales/core.php");

$user_locale = $AppUI->user_locale;

setlocale(LC_TIME, $user_locale);

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

// Don't output anything. Usefull for fileviewers, popup dialogs, ajax requests, etc.
$suppressHeaders = dPgetParam($_GET, "suppressHeaders");

// Output the charset header in cas of an ajax request
$ajax = dPgetParam($_GET, "ajax", false);

if(!$suppressHeaders || $ajax) {
	// output the character set header
	if(isset($locale_char_set)) {
		header("Content-type: text/html;charset=$locale_char_set");
	}
}

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
  
if(!$suppressHeaders) {
  
  // -- Code pour le HEADER --
  
  // Définition du CharSet
  if(!isset( $locale_char_set )){
    $locale_char_set = "UTF-8";
  }
  //Liste des Etablissements
  $etablissements = CMediusers::loadEtablissements(PERM_EDIT);
  // Liste des Modules
  $dialog = dPgetParam( $_GET, "dialog");
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
  require_once( $AppUI->getSystemClass("smartydp"));
  $smartyStyle = new CSmartyDP(1);
  $smartyStyle->template_dir = "style/$uistyle/templates/";
  $smartyStyle->compile_dir  = "style/$uistyle/templates_c/";
  $smartyStyle->config_dir   = "style/$uistyle/configs/";
  $smartyStyle->cache_dir    = "style/$uistyle/cache/";
  if (!$dialog) {
    $smartyStyle->assign("affModule" , $affModule);
  }
  $smartyStyle->assign("includeFooter"        , false);
  $smartyStyle->assign("titleBlockData"       , $titleBlockData);
  $smartyStyle->assign("localeCharSet"        , $locale_char_set);
  $smartyStyle->assign("mediboardVersion"     , @$AppUI->getVersion());
  $smartyStyle->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/favicon.ico",1));
  $smartyStyle->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all",1));
  $smartyStyle->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all",1));
  $smartyStyle->assign("mediboardScript"      , mbLoadScripts(1));
  $smartyStyle->assign("dialog"               , $dialog);
  $smartyStyle->assign("messages"             , $messages);
  $smartyStyle->assign("uistyle"              , $uistyle);
  $smartyStyle->assign("AppUI"                , $AppUI);
  $smartyStyle->assign("Etablissements"       , $etablissements);
  $smartyStyle->assign("helpOnline"           , mbPortalLink($m, "Aide"));
  $smartyStyle->assign("suggestion"           , mbPortalLink("bugTracker", "Suggérer une amélioration"));
  $smartyStyle->assign("errorMessage"         , $AppUI->getMsg());
  $smartyStyle->display("header.tpl");
}

// -- Code pour les tabBox et Inclusion du fichier demandé --

$tab = $a == "index"  ? mbGetValueFromGetOrSession("tab", 1) : mbGetValueFromGet("tab");

if($tab !== null) {
  $currentModule->showTabs();
} else {
  $currentModule->showAction();
}

$phpChrono->stop();

if(!$suppressHeaders) {
  // -- Inclusion du footer --
  if( !function_exists("memory_get_usage") ) {
    function memory_get_usage() {
      return "-";
    }
  }
  $performance = array();
  $performance["genere"] = number_format($phpChrono->total, 3);
  $performance["memoire"] = mbConvertDecaBinary(memory_get_usage());;
  $smartyStyle->assign("includeFooter" , true);
  $smartyStyle->assign("debugMode"     , $dPconfig["debug"]);
  $smartyStyle->assign("performance"   , $performance);
  $smartyStyle->assign("errorMessage"  , $AppUI->getMsg());
  $smartyStyle->assign("demoVersion"   , $dPconfig["demo_version"]);
  $smartyStyle->display("header.tpl");
}

ob_end_flush();

require "./includes/access_log.php";

?>
