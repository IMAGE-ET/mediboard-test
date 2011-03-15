<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::loadClass("CHTMLResourceLoader");

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
    
    if (!$compress || !file_exists("$path/css.list")) {
    	$css_file = "$path/main.css";
      return self::loadFile($css_file, $media, null, filemtime($css_file), $type);
    }
    
    $list = file("$path/css.list", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $result = "";
    
    $files = array();
    foreach($list as $_file) {
      $files[] = "$path/$_file";
    }
    
    $uptodate = false;
    
    $hash = self::getHash(implode("", $files)."-level-$compress");
    $cachefile = "tmp/$hash.$theme.css";
    
    // If it exists, we check if it is up to date
    if (file_exists($cachefile)) {
      $uptodate = true;
      $last_update = filemtime($cachefile);
      foreach($files as $file) {
        if (filemtime($file) > $last_update) {
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
          $content = str_replace(array("\r\n", "\r", "\n", "\t"), "", $content); // whitespace
          $content = preg_replace("/\s*([\{\};:,])\s+/", "$1", $content); // whitespace around { and }
          $content = preg_replace("/;\}/", "}", $content); // ;} >> }
          $content = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $content); // comments
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
}
