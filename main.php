<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage mediboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// This script cannot be called directly
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== FALSE) {
  header("HTTP/1.1 403 Forbidden");
  exit(0);
}

// Get the user's style
$uistyle = CAppUI::pref("UISTYLE");
if (!is_dir("style/$uistyle")) {
  $uistyle = "mediboard";
}

// check if we are logged in
if (!CAppUI::$instance->user_id) {
  $redirect = CValue::get("logout") ?  "" : CValue::read($_SERVER, "QUERY_STRING"); 
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
    $smartyLogin->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico"));
    $smartyLogin->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all"));
    $smartyLogin->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all"));
    $smartyLogin->assign("mediboardScript"      , mbLoadScripts());
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
  @session_destroy(); // Escaped because of an unknown error
  CApp::rip();
}

// Set the module and action from the url
if (null == $m = CAppUI::checkFileName(CValue::get("m", 0))) {
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

$a     = CAppUI::checkFileName(CValue::get("a"     , "index"));
$u     = CAppUI::checkFileName(CValue::get("u"     , ""));
$dosql = CAppUI::checkFileName(CValue::post("dosql", ""));

$tab = $a == "index" ? 
  CValue::getOrSession("tab", 1) : 
  CValue::get("tab");

// Check whether the password is strong enough
if (CAppUI::$instance->weak_password && 
    !CAppUI::$instance->user_remote && 
    !($m == "admin" && $tab == "chpwd")) {
  CAppUI::redirect("m=admin&tab=chpwd&forceChange=1");
}

// set the group in use, put the user group if not allowed
$g = CValue::getOrSessionAbs("g", CAppUI::$instance->user_group);
$indexGroup = new CGroups;
$indexGroup->load($g);
if ($indexGroup->_id) {
  if (!$indexGroup->canRead()) {
    $g = CAppUI::$instance->user_group;
    CValue::setSessionAbs("g", $g);
  }
}

// do some db work if dosql is set
if ($dosql) {
  $mDo = CValue::post("m", $m);
  require("./modules/$mDo/$dosql.php");
}

// Feed modules with tabs
foreach (CModule::getActive() as $module) {
  include("./modules/$module->mod_name/index.php");
}

$currentModule->addConfigureTab();

if (!$a || $a === "index")
  $tab = $currentModule->getValidTab($tab);

if (!$suppressHeaders) {
  // Liste des Etablissements
  $etablissements = CMediusers::loadEtablissements(PERM_EDIT);

  // Messages
  $messages = new CMessage();
  $messages = $messages->loadPublications("present", $m, $g);
  
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
  $smartyHeader->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico"));
  $smartyHeader->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all"));
  $smartyHeader->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all"));
  $smartyHeader->assign("mediboardScript"      , mbLoadScripts());
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
$performance["memoire"]        = CMbString::toDecaBinary(memory_get_usage());
$performance["objets"]         = CMbObject::$objectCount;
$performance["cachableCount"]  = array_sum(CMbObject::$cachableCounts);
$performance["cachableCounts"] = CMbObject::$cachableCounts;

$performance["size"] = CMbString::toDecaBinary(ob_get_length());
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
  $address = get_remote_address();
  
  $smartyFooter = new CSmartyDP("style/$uistyle");
  $smartyFooter->assign("offline"       , false);
  $smartyFooter->assign("debugMode"     , CAppUI::pref("INFOSYSTEM"));
  $smartyFooter->assign("performance"   , $performance);
  $smartyFooter->assign("userIP"        , $address["client"]);
  $smartyFooter->assign("errorMessage"  , CAppUI::getMsg());
  $smartyFooter->display("footer.tpl");
}

// Ajax performance
if ($ajax) {
  $tplAjax = new CSmartyDP("modules/system");
  $tplAjax->assign("performance", $performance);
  $tplAjax->display("ajax_errors.tpl");
}
