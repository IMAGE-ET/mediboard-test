<?php /* $Id$ */

/**
* @package Mediboard
* @version $Revision$
* @author Romain Ollivier
*/

$user = CUser::get();

CAppUI::requireLibraryFile("phpThumb/phpthumb.class");
include_once "lib/phpThumb/phpThumb.config.php";

//require_once("./lib/phpThumb/phpthumb.class.php");
//trigger_error("Source is $file->_file");
    
ob_clean();

// Direct acces needs Administrator rights
$file_path = CValue::get("file_path");
if ($file_path) {
  $file_size = filesize($file_path);
  $file_type = "text/xml";
  $file_name = basename($file_path);
  
  if ($user->user_type == 1) {
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
    CAppUI::setMsg("Permissions administrateur obligatoire", UI_MSG_ERROR);
    CAppUI::redirect();
  }
}

if ($file_id = CValue::get("file_id")) {
  $file = new CFile();
  $file->load($file_id);
  $file->loadRefsFwd();
  if (!is_file($file->_file_path)) {
    header("Location: images/pictures/notfound.png");
    return;
  } elseif (!$file->canRead()) {
    header("Location: images/pictures/accessdenied.png");
    return;
  }
  
  if (CValue::get("phpThumb")) {
    
    $w  = CValue::get("w" , "");
    $h  = CValue::get("h" , "");
    $zc = CValue::get("zc" , "");
    $hp = CValue::get("hp", "");
    $wl = CValue::get("wl", "");
    $f  = CValue::get("f" , "jpg");
    $q  = CValue::get("q" , 80);
    $dpi = CValue::get("dpi" , 150);
    $sfn = CValue::get("sfn" , 0);
    
    //creation fin URL
    $finUrl="";

    if($f){ $finUrl.="&f=$f";}    
    if($q){ $finUrl.="&q=$q";}  

    if (strpos($file->file_type, "image") !== false && strpos($file->file_type,"svg") == false) {
      if($hp){$finUrl.="&hp=$hp";}
      if($wl){$finUrl.="&wl=$wl";}
      if($h){$finUrl.="&h=$h";}
      if($w){$finUrl.="&w=$w";}
      if($zc){$finUrl.="&zc=$zc";}
      //trigger_error("Source is $file->_file_path$finUrl");
      header("Location: lib/phpThumb/phpThumb.php?src=$file->_file_path".$finUrl);
    } elseif (strpos($file->file_type, "pdf") !== false) {
      
      if($hp){$finUrl.="&h=$hp";}
      if($wl){$finUrl.="&w=$wl";}
      
      if($sfn){$finUrl.="&sfn=$sfn";}
      if($dpi){$finUrl.="&dpi=$dpi";}
      
      if($file->oldImageMagick() && ($file->rotation % 180 == 90)) {
        $w = intval($w * sqrt(2));
      }
      
      $finUrl .= "&ra={$file->rotation}";
      
      if($h){$finUrl.="&h=$h";}
      if($w){$finUrl.="&w=$w";}

      header("Location: lib/phpThumb/phpThumb.php?src=$file->_file_path".$finUrl);

    } elseif ($file->isPDFconvertible()) {
      if($hp){$finUrl.="&h=$hp";}
      if($wl){$finUrl.="&w=$wl";}
      if($h){$finUrl.="&h=$h";}
      if($w){$finUrl.="&w=$w";}
      if($sfn){$finUrl.="&sfn=$sfn";}
      if($dpi){$finUrl.="&dpi=$dpi";}
      
      $fileconvert = $file->loadPDFconverted();
      $success = 1;
      if (!$fileconvert->_id) {
        $success = $file->convertToPDF();
      }
      if ($success == 1) {
        $fileconvert = $file->loadPDFconverted();
        header("Location: lib/phpThumb/phpThumb.php?src=$fileconvert->_file_path".$finUrl);
      }
      else {
        header("Location: images/pictures/medifile.png");
      }
    } else {
      header("Location: images/pictures/medifile.png");
    }
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

    header('Content-disposition: inline; filename="'.$file->file_name.'"');
    readfile($file->_file_path);
  }
} else {
  CAppUI::setMsg("fileIdError", UI_MSG_ERROR);
  CAppUI::redirect();
}
?>