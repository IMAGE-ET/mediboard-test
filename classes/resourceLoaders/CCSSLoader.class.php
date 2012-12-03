<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * CSS resource loader utility class
 */
abstract class CCSSLoader extends CHTMLResourceLoader {
  
  /**
   * Links a style sheet
   * Only to be called while in the HTML header
   * 
   * @param string $file  Filename of the stylesheet
   * @param string $media A valid CSS media query
   * @param string $cc    An IE conditional comment
   * @param string $build A build number
   * @param string $type  The mime type to use to load the stylesheet
   * 
   * @return string A <link> tag to load the stylesheet
   */
  static function loadFile($file, $media = null, $cc = null, $build = null, $type = "text/css") {
    $tag = self::getTag(
      "link", 
      array(
        "type"  => $type,
        "rel"   => "stylesheet",
        "href"  => "$file?".self::getBuild($build),
        "media" => $media,
      )
    );
    
    return self::conditionalComments($tag, $cc);
  }
  
  /**
   * Builds a list of HTML <link> tags to load the stylesheets of the theme
   * 
   * @param string $theme The theme name
   * @param string $media A valid CSS media query
   * @param string $type  The mime type to load the stylesheets
   * 
   * @return string A list of HTML <link> tags 
   */
  static function loadFiles($theme = "mediboard", $media = "all", $type = "text/css") {
    $compress = CAppUI::conf("minify_css");
    
    if ($theme == "modules") {
      $files = glob("modules/*/css/main.css");
    }
    else {
      if ($theme == "mobile") {
        $path = "mobile/style";
      }
      else {
        $path = "style/$theme";
      }
      
      /*if (!$compress) {
        $css_file = "$path/main.css";
        return self::loadFile($css_file, $media, null, self::getLastChange($css_file), $type);
      }*/
      
      if (file_exists("$path/css.list")) {
        $list = file("$path/css.list", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $list = array_map("trim", $list);
      }
      else {
        $list = array("main.css");
      }
    
      $files = array();
      foreach ($list as $_file) {
        $files[] = "$path/$_file";
      }
    }
    
    $result = "";
    $uptodate = false;
    
    $hash = self::getHash(implode("", $files)."-level-$compress");
    $cachefile = "tmp/$hash-$theme.css";
    
    // If it exists, we check if it is up to date
    if (file_exists($cachefile)) {
      $uptodate = true;
      $last_update = self::getLastChange($cachefile);
      foreach ($files as $file) {
        if (self::getLastChange($file) > $last_update) {
          $uptodate = false;
          break;
        }
      }
    }
    
    if (!$uptodate) {
      $all = "";
      foreach ($files as $_file) {
        $_path = dirname($_file);
        $content = file_get_contents($_file);
        $content = preg_replace("/\@import\s+(?:url\()?[\"']?([^\"\'\)]+)[\"']?\)?;/i", "", $content); // remove @imports
        $content = preg_replace("/(url\s*\(\s*[\"\']?)/", "$1../$_path/", $content); // relative paths
        
        $all .= $content."\n";
      }
        
      if ($compress == 2) {
        $all = self::minify($all);
      }
      
      file_put_contents($cachefile, $all);
      $last_update = time();
    }
    
    $result .= self::loadFile($cachefile, $media, null, $last_update, $type)."\n";
    
    return $result;
  }
  
  /**
   * Simple home-made CSS minifier
   * 
   * @param string $css The CSS code
   * 
   * @return string The minified CSS code
   */
  static function minify($css) {
    $css = str_replace(array("\r\n", "\r", "\n", "\t"), "", $css); // whitespace
    $css = preg_replace("/\s+/", " ", $css); // multiple spaces
    $css = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $css); // comments
    $css = preg_replace("/\s*([\{\};:,>])\s*/", "$1", $css); // whitespace around { } ; : , >
    $css = str_replace(";}", "}", $css); // ;} >> }
    $css = str_replace("/./", "/", $css); // /./ >> /
    $css = CMbPath::reduce($css); // foo/../ >> /
    //$css = preg_replace("/#([0-9A-F])\\1([0-9A-F])\\2([0-9A-F])\\3/i", "#\\1\\2\\3", $css); // Reduce #6699FF to #69F
    return $css;
  }
}
