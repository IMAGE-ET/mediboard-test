<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage mediboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$debug = CAppUI::pref("INFOSYSTEM");

// Get the user's style
$uistyle = CAppUI::pref("UISTYLE");
if (!file_exists("style/$uistyle/templates/header.tpl")) {
  $uistyle = "mediboard";
}

CJSLoader::$files = array(
  CJSLoader::getLocaleFile(),
  "includes/javascript/printf.js",
  //"lib/dshistory/dshistory.js",
  
  "lib/scriptaculous/lib/prototype.js",
  "lib/scriptaculous/src/scriptaculous.js",
  
  /*"lib/nwmatcher/nwmatcher.js",
  "lib/nwmatcher/traversal.js",
  "lib/nwmatcher/prototypejs.js",*/
 
  "includes/javascript/console.js",

  // We force the download of the dependencies 
  "lib/scriptaculous/src/builder.js",
  "lib/scriptaculous/src/effects.js",
  "lib/scriptaculous/src/dragdrop.js",
  "lib/scriptaculous/src/controls.js",
  "lib/scriptaculous/src/slider.js",
  "lib/scriptaculous/src/sound.js",

  "includes/javascript/prototypex.js",

  // Datepicker
  "includes/javascript/date.js",
  "lib/datepicker/datepicker.js",
  "lib/datepicker/datepicker-locale-fr_FR.js",

  // Livepipe UI
  "lib/livepipe/livepipe.js",
  "lib/livepipe/tabs.js",
  "lib/livepipe/window.js",

  // Flotr
  "lib/flotr/flotr.js",
  "lib/flotr/lib/excanvas.js",
  "lib/flotr/lib/base64.js",
  "lib/flotr/lib/canvas2image.js",
  "lib/flotr/lib/canvastext.js",

  "includes/javascript/common.js",
  "includes/javascript/functions.js",
  "includes/javascript/tooltip.js",
  "includes/javascript/controls.js",
  "includes/javascript/cookies.js",
  "includes/javascript/url.js",
  "includes/javascript/forms.js",
  "includes/javascript/checkForms.js",
  "includes/javascript/aideSaisie.js",

  "includes/javascript/mbmail.js",
);

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
    $tplLogin = new CSmartyDP("style/$uistyle");
    $tplLogin->assign("localeInfo"           , $locale_info);
    $tplLogin->assign("mediboardShortIcon"   , CFaviconLoader::loadFile("style/$uistyle/images/icons/favicon.ico"));
    $tplLogin->assign("mediboardCommonStyle" , CCSSLoader::loadFile("style/mediboard/main.css", "all"));
    $tplLogin->assign("mediboardStyle"       , CCSSLoader::loadFile("style/$uistyle/main.css", "all"));
    $tplLogin->assign("mediboardScript"      , CJSLoader::loadFiles(!$debug));
    $tplLogin->assign("errorMessage"         , CAppUI::getMsg());
    $tplLogin->assign("time"                 , time());
    $tplLogin->assign("redirect"             , $redirect);
    $tplLogin->assign("uistyle"              , $uistyle);
    $tplLogin->assign("browser"              , $browser);
    $tplLogin->assign("nodebug"              , true);
    $tplLogin->assign("offline"              , false);
    $tplLogin->display("login.tpl");
  }
  
  // Destroy the current session and output login page
  session_unset();
  @session_destroy(); // Escaped because of an unknown error
  CApp::rip();
}

// Set the module and action from the url
if (null == $m = CAppUI::checkFileName(CValue::get("m", 0))) {
  $m = CPermModule::getFirstVisibleModule();
  $pref_module = CAppUI::pref("DEFMODULE");
  if ($pref_module && CPermModule::getViewModule(CModule::getInstalled($pref_module)->mod_id, PERM_READ)) {
    $m = $pref_module;
  }
}

// Still no target module
if (null == $m) {
  CAppUI::redirect("m=system&a=access_denied");
}

if (null == $module = CModule::getInstalled($m)) {
  CAppUI::redirect("m=system&a=module_missing&module=$m");
}

// Get current module permissions
// these can be further modified by the included action files
$can = $module->canDo();

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
if ($indexGroup->load($g) && !$indexGroup->canRead()) {
  $g = CAppUI::$instance->user_group;
  CValue::setSessionAbs("g", $g);
}

// do some db work if dosql is set
if ($dosql) {
  $mDo = CValue::post("m", $m);
  if(is_file("./modules/$mDo/controllers/$dosql.php")) {
    require("./modules/$mDo/controllers/$dosql.php");
  } else {
    require("./modules/$mDo/$dosql.php");
  }
}

// Feed module with tabs
include("./modules/{$module->mod_name}/index.php");
if ($tab !== null) {
  $module->addConfigureTab();
}
  
if (!$a || $a === "index")
  $tab = $module->getValidTab($tab);

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
  $tplHeader = new CSmartyDP("style/$uistyle");
  
  $tplHeader->assign("offline"              , false);
  $tplHeader->assign("nodebug"              , true);
  $tplHeader->assign("configOffline"        , null);
  $tplHeader->assign("localeInfo"           , $locale_info);
  $tplHeader->assign("mediboardShortIcon"   , CFaviconLoader::loadFile("style/$uistyle/images/icons/favicon.ico"));
  $tplHeader->assign("mediboardCommonStyle" , CCSSLoader::loadFile("style/mediboard/main.css", "all"));
  $tplHeader->assign("mediboardStyle"       , CCSSLoader::loadFile("style/$uistyle/main.css", "all"));
  $tplHeader->assign("mediboardScript"      , CJSLoader::loadFiles(!$debug));
  $tplHeader->assign("dialog"               , $dialog);
  $tplHeader->assign("messages"             , $messages);
  $tplHeader->assign("mails"                , $mails);
  $tplHeader->assign("uistyle"              , $uistyle);
  $tplHeader->assign("browser"              , $browser);
  $tplHeader->assign("errorMessage"         , CAppUI::getMsg());
  $tplHeader->assign("Etablissements"       , $etablissements);
  $tplHeader->assign("svnStatus"            , $svnStatus);
  $tplHeader->assign("portal"               , array (
    "help" => mbPortalURL($m, $tab),
    "tracker" => mbPortalURL("tracker"),
  ));
  
  $tplHeader->display("header.tpl");
}

// tabBox et inclusion du fichier demandé
if ($tab !== null) {
  $module->showTabs();
} else {
  $module->showAction();
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
  
  $tplFooter = new CSmartyDP("style/$uistyle");
  $tplFooter->assign("offline"       , false);
  $tplFooter->assign("debugMode"     , $debug);
  $tplFooter->assign("performance"   , $performance);
  $tplFooter->assign("userIP"        , $address["client"]);
  $tplFooter->assign("errorMessage"  , CAppUI::getMsg());
  $tplFooter->display("footer.tpl");
}

// Ajax performance
if ($ajax) {
  $tplAjax = new CSmartyDP("modules/system");
  $tplAjax->assign("performance", $performance);
  $tplAjax->display("ajax_errors.tpl");
}
