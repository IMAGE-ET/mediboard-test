<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $locale_char_set, $uistyle, $messages, $dPconfig;

if(!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}
set_time_limit(90);

$mode = mbGetValueFromGet("mode" , null);

// Création du fichier Zip
if(file_exists("tmp/mediboard_repas.zip")){unlink("tmp/mediboard_repas.zip");}
$zipFile = new Archive_Zip("tmp/mediboard_repas.zip");


// Création du fichier index.html
$plats     = new CPlat;

$smarty = new CSmartyDP(1);
$smarty->assign("plats" , $plats);
$smarty->assign("mediboardScriptStorage", mbLoadScriptsStorage(1));

$smartyStyle = new CSmartyDP(1);

$smartyStyle->template_dir = "style/$uistyle/templates/";
$smartyStyle->compile_dir  = "style/$uistyle/templates_c/";
$smartyStyle->config_dir   = "style/$uistyle/configs/";
$smartyStyle->cache_dir    = "style/$uistyle/cache/";
 
$smartyStyle->assign("offline"              , true);
$smartyStyle->assign("localeCharSet"        , $locale_char_set);
$smartyStyle->assign("mediboardVersion"     , @$AppUI->getVersion());
$smartyStyle->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico",1));
$smartyStyle->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all",1));
$smartyStyle->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all",1));
$smartyStyle->assign("mediboardScript"      , mbLoadScripts(1));
$smartyStyle->assign("messages"             , $messages);
$smartyStyle->assign("debugMode"            , @$AppUI->user_prefs["INFOSYSTEM"]);
$smartyStyle->assign("demoVersion"          , $dPconfig["demo_version"]);
$smartyStyle->assign("baseUrl"              , $dPconfig["base_url"]);
$smartyStyle->assign("errorMessage"         , $AppUI->getMsg());
$smartyStyle->assign("uistyle"              , $uistyle);

ob_start();
$smartyStyle->display("header.tpl");
$smarty->display("repas_offline.tpl");
$smartyStyle->display("footer.tpl");
$indexFile = ob_get_contents();
ob_end_clean();
file_put_contents("tmp/index.html", $indexFile);

$zipFile->add("tmp/index.html"       , array("remove_path"=>"tmp/"));
if($mode == "index"){
  return;  
}



function delSvnAndSmartyDir($action,$fileProps){
 if(preg_match("/.svn/",$fileProps["filename"]) 
 || preg_match("/templates/",$fileProps["filename"]) 
 || preg_match("/templates_c/",$fileProps["filename"])){
  return false;
 }else{
   return true;
 }
}

$zipFile->add("images/"                     , array("callback_pre_add"=>"delSvnAndSmartyDir"));
$zipFile->add("style/"                      , array("callback_pre_add"=>"delSvnAndSmartyDir"));
$zipFile->add("includes/javascript/"        , array("callback_pre_add"=>"delSvnAndSmartyDir"));
$zipFile->add("modules/dPrepas/javascript/" , array("callback_pre_add"=>"delSvnAndSmartyDir"));

$zipFile->add("lib/dojo");
$zipFile->add("lib/jscalendar");
$zipFile->add("lib/rico");
$zipFile->add("lib/scriptaculous");

mbtrace($zipFile->listContent(), "Contenu de l'archive");

?>