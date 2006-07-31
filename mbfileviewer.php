<?php /* $Id$ */

/**
* @package Mediboard
* @version $Revision$
* @author Romain Ollivier
*/

$dPconfig = array();

if (!is_file("./includes/config.php")) {
  header("location: install/");
  die("Redirection vers l'assistant d'installation");
}

require_once("./classes/ui.class.php");
require_once("./includes/config.php");
require_once("./lib/phpThumb/phpthumb.class.php");

// Check that the user has correctly set the root directory
is_file($dPconfig["root_dir"]."/includes/config.php") 
  or die("ERREUR FATALE: le repertoire racine est probablement mal configuré");

require_once("./includes/main_functions.php");
require_once("./includes/errors.php");

// PHP Configuration
ini_set("memory_limit", "64M");

// Manage the session variable(s)
session_name("dotproject");
if (get_cfg_var("session.auto_start") > 0) {
  session_write_close();
}
session_start();
session_register("AppUI"); 
  
// Check if session has previously been initialised
if (!isset($_SESSION["AppUI"]) || isset($_GET["logout"])) {
    $_SESSION["AppUI"] = new CAppUI();
}

$AppUI =& $_SESSION["AppUI"];
$AppUI->setConfig($dPconfig);

require "./includes/db_connect.php";

// load the commonly used classes
require_once($AppUI->getSystemClass("date"));
require_once($AppUI->getSystemClass("dp"));
require_once($AppUI->getSystemClass("mbmodule"));

// Direct acces needs Administrator rights
$file_path = mbGetValueFromGet("file_path");
if ($file_path) {
  $file_size = filesize($file_path);
  $file_type = "text/xml";
  $file_name = basename($file_path);
  
  if ($AppUI->user_type == 1) {
    // BEGIN extra headers to resolve IE caching bug (JRP 9 Feb 2003)
    // [http://bugs.php.net/bug.php?id=16173]
    header("Pragma: ");
    header("Cache-Control: ");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");  //HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    // END extra headers to resolve IE caching bug
  
    header("MIME-Version: 1.0");
    header("Content-length: $file_size");
    header("Content-type: $file_type");
    header("Content-disposition: inline; filename=$file_name");
    readfile($file_path);
    return;
  } else {
    $AppUI->setMsg("Permissions administrateur obligatoire", UI_MSG_ERROR);
    $AppUI->redirect();
  }
}

// Check permissions on dPcabinet. to be refactored with PEAR::Auth
include "./includes/permissions.php";
$canRead = !getDenyRead("dPcabinet");
if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

require_once($AppUI->getModuleClass("dPfiles", "files"));

if ($file_id = mbGetValueFromGet("file_id")) {
  $file = new CFile();
  $file->load($file_id);
  if (!is_file($file->_file_path)) {
    $AppUI->setMsg("Fichier manquant", UI_MSG_ERROR);
    $AppUI->redirect();
  }
  
  if(mbGetValueFromGet("phpThumb")) {
    $w  = mbGetValueFromGet("w" , "");
    $h  = mbGetValueFromGet("h" , "");
    $hp = mbGetValueFromGet("hp", "");
    $wl = mbGetValueFromGet("wl", "");
    $f  = mbGetValueFromGet("f" , "jpg");
    $q  = mbGetValueFromGet("q" , 90);
    //creation fin URL
    $finUrl="";
    if($f){ $finUrl.="&f=$f";}    
    if($q){ $finUrl.="&q=$q";}  
    
    if(strpos($file->file_type, "image") !== false) {
      if($hp){$finUrl.="&hp=$hp";}
      if($wl){$finUrl.="&wl=$wl";}
      if($h){$finUrl.="&h=$h";}
      if($w){$finUrl.="&w=$w";}
      header("Location: lib/phpThumb/phpThumb.php?src=$file->_file_path".$finUrl);
    } elseif(strpos($file->file_type, "pdf") !== false) {
      if($hp){$finUrl.="&h=$hp";}
      if($wl){$finUrl.="&w=$wl";}
      if($h){$finUrl.="&h=$h";}
      if($w){$finUrl.="&w=$w";}
      header("Location: lib/phpThumb/phpThumb.php?src=$file->_file_path".$finUrl);
      //header("Location: modules/dPfiles/images/acroread.png");
    } elseif(strpos($file->file_type, "text") !== false) {
      header("Location: modules/dPfiles/images/text.png");
    } elseif(strpos($file->file_type, "msword") !== false) {
      header("Location: modules/dPfiles/images/text.png");
    } elseif(strpos($file->file_type, "video") !== false) {
      header("Location: modules/dPfiles/images/video.png");
    } else {
      header("Location: modules/dPfiles/images/unknown.png");
    }
    /*
    $thumb = new phpthumb;
    $thumb->setSourceData(file_get_contents($file->_file_path));
    $thumb->setParameter("hp", 64);
    $thumb->setParameter("wl", 64);
    $thumb->GenerateThumbnail();
    $thumb->OutputThumbnail();*/
  } else {

    // BEGIN extra headers to resolve IE caching bug (JRP 9 Feb 2003)
    // [http://bugs.php.net/bug.php?id=16173]
    header("Pragma: ");
    header("Cache-Control: ");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");  //HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    // END extra headers to resolve IE caching bug

    header("MIME-Version: 1.0");
    header("Content-length: {$file->file_size}");
    header("Content-type: {$file->file_type}");
    header("Content-disposition: inline; filename={$file->file_name}");
    readfile($file->_file_path);
  }
} else {
  $AppUI->setMsg("fileIdError", UI_MSG_ERROR);
  $AppUI->redirect();
}
?>
