<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $locale_char_set, $uistyle, $messages, $dPconfig, $version;

$can->needsRead();

set_time_limit(90);

$indexFile  = mbGetValueFromPost("indexFile"  , 0);
$style      = mbGetValueFromPost("style"      , 0);
$image      = mbGetValueFromPost("image"      , 0);
$javascript = mbGetValueFromPost("javascript" , 0);
$lib        = mbGetValueFromPost("lib"        , 0);
$typeArch   = mbGetValueFromPost("typeArch"   , "zip");

// Création du fichier Zip
if(file_exists("tmp/mediboard_repas.zip")){unlink("tmp/mediboard_repas.zip");}
if(file_exists("tmp/mediboard_repas.tar.gz")){unlink("tmp/mediboard_repas.tar.gz");}

if($typeArch == "zip"){
  $zipFile = new Archive_Zip("tmp/mediboard_repas.zip");
}elseif($typeArch == "tar"){
  $zipFile = new Archive_Tar("tmp/mediboard_repas.tar.gz", true);
}else{
 return; 
}


if($indexFile){
  // Création du fichier index.html
  $plats     = new CPlat;  
  
  $configOffline = array("urlMediboard" => $dPconfig["base_url"]."/",
                         "etatOffline"  => 0);
  
  $smarty = new CSmartyDP();
  $smarty->template_dir = "modules/dPrepas/templates/";
  $smarty->compile_dir  = "modules/dPrepas/templates_c/";
  $smarty->config_dir   = "modules/dPrepas/configs/";
  $smarty->cache_dir    = "modules/dPrepas/cache/";
  $smarty->assign("plats" , $plats);
  $smarty->assign("mediboardScriptStorage", mbLoadScriptsStorage(1));
  
  $smartyStyle = new CSmartyDP();
  $smartyStyle->template_dir = "style/$uistyle/templates/";
  $smartyStyle->compile_dir  = "style/$uistyle/templates_c/";
  $smartyStyle->config_dir   = "style/$uistyle/configs/";
  $smartyStyle->cache_dir    = "style/$uistyle/cache/";
  
  $smartyStyle->assign("offline"              , true);
  $smartyStyle->assign("localeCharSet"        , $locale_char_set);
  $smartyStyle->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico",1));
  $smartyStyle->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all",1));
  $smartyStyle->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all",1));
  $smartyStyle->assign("mediboardScript"      , mbLoadScripts(1));
  $smartyStyle->assign("messages"             , $messages);
  $smartyStyle->assign("debugMode"            , @$AppUI->user_prefs["INFOSYSTEM"]);
  $smartyStyle->assign("configOffline"        , $configOffline);
  $smartyStyle->assign("errorMessage"         , $AppUI->getMsg());
  $smartyStyle->assign("uistyle"              , $uistyle);
  
  ob_start();
  $smartyStyle->display("header.tpl");
  $smarty->display("repas_offline.tpl");
  $smartyStyle->display("footer.tpl");
  $indexFile = ob_get_contents();
  ob_end_clean();
  file_put_contents("tmp/index.html", $indexFile);
  
  if($typeArch == "zip"){
    $zipFile->add("tmp/index.html", array("remove_path"=>"tmp/"));
  }elseif($typeArch == "tar"){
    $zipFile->addModify("tmp/index.html", null, "tmp/");
  }
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


if($style){
  $zipFile->add("style/" , array("callback_pre_add"=>"delSvnAndSmartyDir"));
}

if($image) {
  $zipFile->add("images/" , array("callback_pre_add"=>"delSvnAndSmartyDir"));
}

if($lib){
  $zipFile->add("lib/dojo");
  $zipFile->add("lib/datepicker");
  $zipFile->add("lib/scriptaculous");
}

if($javascript){
  $zipFile->add("includes/javascript/"        , array("callback_pre_add"=>"delSvnAndSmartyDir"));
  $zipFile->add("modules/dPrepas/javascript/" , array("callback_pre_add"=>"delSvnAndSmartyDir"));
}

mbtrace($zipFile->listContent(), "Contenu de l'archive");
CApp::rip();
?>