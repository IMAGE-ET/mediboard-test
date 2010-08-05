<?php /* $Id$ */

/**
* @package Mediboard
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;
CAppUI::requireLibraryFile("phpThumb/phpthumb.class");
include_once "lib/phpThumb/phpThumb.config.php";
//require_once("./lib/phpThumb/phpthumb.class.php");
//trigger_error("Source is $file->_file");

// Direct acces needs Administrator rights
$file_path = CValue::get("file_path");
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
  // Liste des extensions dont la conversion PDF est possible
  $file_types = array(
    "cgm", "csv", "dbf", "dif", "doc", "docm", "docx", "dot", "dotm", "dotx",
    "dxf", "emf", "eps", "fodg", "fodp", "fods", "fodt", "htm", "html", "hwp",
    "lwp", "met", "mml", "odp", "odg", "ods", "otg", "odf", "odm", "odt", "oth",
    "otp", "ots", "ott", "pct", "pict", "pot", "potm", "potx", "pps", "ppt", "pptm",
    "pptx", "rtf", "sgf", "sgv", "slk", "stc", "std", "sti", "stw", "svg", "svm", "sxc",
    "sxd", "sxg", "sxi", "sxm", "sxw", "txt", "uof", "uop", "uos", "uot", "wb2", "wk1", "wks",
    "wmf", "wpd", "wpg", "wps", "xlc", "xlm", "xls", "xlsb", "xlsm", "xlsx", "xlt", "xltm",
    "xltx", "xlw", "xml");
  global $dPconfig;

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
    $ra  = CValue::get("ra", 0);
    
    //creation fin URL
    $finUrl="";

    //if($ra == 90 || $ra == 270) { $w = 1000;}
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
      
      // Dans le cas de la tooltip, il faudrait aussi interchanger $w et $h.
      // Mais cela rentre en conflit avec l'aperçu de CFiles
      $file->loadNbPages();
      $ra += $file->_rotate;
      
      $finUrl .= "&ra=$ra";
        
      if($h){$finUrl.="&h=$h";}
      if($w){$finUrl.="&w=$w";}
      // Sharp filter to unblur raster
//      $finUrl .= "&fltr[]=usm|80|5|1";
//      $finUrl .= "&fltr[]=usm";
      header("Location: lib/phpThumb/phpThumb.php?src=$file->_file_path".$finUrl);
      //header("Location: images/pictures/acroread.png");
    } elseif (in_array(substr(strrchr($file->file_name, '.'),1), $file_types) && $dPconfig["dPfiles"]["CFile"]["active_oxoffice"]) {
      if($hp){$finUrl.="&h=$hp";}
      if($wl){$finUrl.="&w=$wl";}
      if($h){$finUrl.="&h=$h";}
      if($w){$finUrl.="&w=$w";}
      if($sfn){$finUrl.="&sfn=$sfn";}
      if($dpi){$finUrl.="&dpi=$dpi";}
      $fileb = new CFile();
      $fileb->object_class = "CFile";
      $fileb->object_id = $file->_id;
      $success = 1;
      $fileb->loadMatchingObject();
      if (!$fileb->_id) {
        $success = $file->convertToPDF(strrchr($file->file_name, '.'));
      }
      if ($success == 1) {
        $fileb->loadMatchingObject();
        header("Location: lib/phpThumb/phpThumb.php?src=$fileb->_file_path".$finUrl);
      }
      else {
        header("Location: images/pictures/medifile.png");
      }
    }/*elseif(strpos($file->file_type, "csv") !== false) {
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
    } */else {
      header("Location: images/pictures/medifile.png");
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
  CAppUI::setMsg("fileIdError", UI_MSG_ERROR);
  CAppUI::redirect();
}
?>