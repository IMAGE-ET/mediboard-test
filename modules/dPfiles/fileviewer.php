<?php /* $Id: mbfileviewer.php 587 2006-08-10 07:46:47Z maskas $ */

/**
* @package Mediboard
* @version $Revision: 587 $
* @author Romain Ollivier
*/

global $AppUI;
CAppUI::requireLibraryFile("phpThumb/phpthumb.class");
//require_once("./lib/phpThumb/phpthumb.class.php");
//trigger_error("Source is $file->_file");

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
    header("Content-disposition: attachment; filename=\"".$file_name."\"");
    readfile($file_path);
    return;
  } else {
    $AppUI->setMsg("Permissions administrateur obligatoire", UI_MSG_ERROR);
    $AppUI->redirect();
  }
}

if($file_id = mbGetValueFromGet("file_id")) {
  $file = new CFile();
  $file->load($file_id);
  $file->loadRefsFwd();
  if(!is_file($file->_file_path)) {
    header("Location: images/pictures/notfound.png");
    return;
  } elseif(!$file->canRead()) {
    header("Location: images/pictures/accessdenied.png");
    return;
  }
  
  if(mbGetValueFromGet("phpThumb")) {
    $w  = mbGetValueFromGet("w" , "");
    $h  = mbGetValueFromGet("h" , "");
    $hp = mbGetValueFromGet("hp", "");
    $wl = mbGetValueFromGet("wl", "");
    $f  = mbGetValueFromGet("f" , "jpg");
    $q  = mbGetValueFromGet("q" , 100);
    $dpi  = mbGetValueFromGet("dpi" , 150);
    $sfn  = mbGetValueFromGet("sfn" , 0);
    //creation fin URL
    $finUrl="";
    if($f){ $finUrl.="&f=$f";}    
    if($q){ $finUrl.="&q=$q";}  
    
    if(strpos($file->file_type, "image") !== false) {
      if($hp){$finUrl.="&hp=$hp";}
      if($wl){$finUrl.="&wl=$wl";}
      if($h){$finUrl.="&h=$h";}
      if($w){$finUrl.="&w=$w";}
      //trigger_error("Source is $file->_file_path$finUrl");
      header("Location: lib/phpThumb/phpThumb.php?src=$file->_file_path".$finUrl);
    } elseif(strpos($file->file_type, "pdf") !== false) {
      if($hp){$finUrl.="&h=$hp";}
      if($wl){$finUrl.="&w=$wl";}
      if($h){$finUrl.="&h=$h";}
      if($w){$finUrl.="&w=$w";}
      if($sfn){$finUrl.="&sfn=$sfn";}
      if($dpi){$finUrl.="&dpi=$dpi";}
      // Sharp filter to unblur raster
//      $finUrl .= "&fltr[]=usm|80|5|1";
//      $finUrl .= "&fltr[]=usm";
      header("Location: lib/phpThumb/phpThumb.php?src=$file->_file_path".$finUrl);
      //header("Location: images/pictures/acroread.png");
    } elseif(strpos($file->file_type, "csv") !== false) {
      header("Location: images/pictures/spreadsheet.png");
    } elseif(strpos($file->file_type, "xls") !== false) {
      header("Location: images/pictures/spreadsheet.png");
    } elseif(strpos($file->file_type, "ods") !== false) {
      header("Location: images/pictures/spreadsheet.png");
    } elseif(strpos($file->file_type, "text") !== false) {
      header("Location: images/pictures/text.png");
    } elseif(strpos($file->file_type, "msword") !== false) {
      header("Location: images/pictures/text.png");
    } elseif(strpos($file->file_type, "video") !== false) {
      header("Location: images/pictures/video.png");
    } else {
      header("Location: images/pictures/unknown.png");
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
    header("Content-disposition: attachment; filename=\"".$file->file_name."\"");
    readfile($file->_file_path);
    
  }
} else {
  $AppUI->setMsg("fileIdError", UI_MSG_ERROR);
  $AppUI->redirect();
}
?>