<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("resourceLoaders/CHTMLResourceLoader");

abstract class CCSSLoader extends CHTMLResourceLoader {
  
  /**
   * Links a style sheet
   * Only to be called while in the HTML header
   */
  static function loadFile($file, $media = null, $cc = null, $build = null, $type = "text/css") {
    $tag = self::getTag("link", array(
      "type"  => $type,
      "rel"   => "stylesheet",
      "href"  => "$file?".self::getBuild($build),
      "media" => $media,
    ));
    
    return self::conditionalComments($tag, $cc);
  }
  
  static function loadFiles($theme = "mediboard", $media = "all", $type = "text/css") {
    $path = "style/$theme";
    $compress = CAppUI::conf("minify_css");
    
    if (!$compress) {
      $css_file = "$path/main.css";
      return self::loadFile($css_file, $media, null, self::getLastChange($css_file), $type);
    }
    
    if(file_exists("$path/css.list")) {
      $list = file("$path/css.list", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      $list = array_map("trim", $list);
    }
    else {
      $list = array("main.css");
    }
    
    $result = "";
    
    $files = array();
    foreach($list as $_file) {
      $files[] = "$path/$_file";
    }
    
    $uptodate = false;
    
    $hash = self::getHash(implode("", $files)."-level-$compress");
    $cachefile = "tmp/$hash-$theme.css";
    
    // If it exists, we check if it is up to date
    if (file_exists($cachefile)) {
      $uptodate = true;
      $last_update = self::getLastChange($cachefile);
      foreach($files as $file) {
        if (self::getLastChange($file) > $last_update) {
          $uptodate = false;
          break;
        }
      }
    }
    
    if (!$uptodate) {
      $all = "";
      foreach($files as $file) {
        $content = file_get_contents($file);
        
        if ($compress == 2) {
          $content = self::minify($content);
        }
        
        $content = preg_replace("/\@import\s+(?:url\()?[\"']?([^\"\'\)]+)[\"']?\)?;/i", "", $content); // remove @imports
        $content = preg_replace("/(url\s*\(\s*[\"\']?)/", "$1../$path/", $content); // relative paths
        $all .= $content."\n";
      }
      
      file_put_contents($cachefile, $all);
      $last_update = time();
    }
    
    $result .= self::loadFile($cachefile, $media, null, $last_update, $type)."\n";
    
    return $result;
  }
  
  static function minify($css) {
    $css = str_replace(array("\r\n", "\r", "\n", "\t"), "", $css); // whitespace
    $css = preg_replace("/\s+/", " ", $css); // multiple spaces
    $css = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $css); // comments
    $css = preg_replace("/\s*([\{\};:,>])\s*/", "$1", $css); // whitespace around { } ; : , >
    $css = str_replace(";}", "}", $css); // ;} >> }
    return $css;
  }
  
  private static function reducePath($matches) {
    mbTrace($matches, "m", true);
    return CMbPath::reduce($matches[1]);
  }
}
