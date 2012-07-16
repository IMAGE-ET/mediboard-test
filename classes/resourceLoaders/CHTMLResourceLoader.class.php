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
 * HTML resource loader utility class
 */
abstract class CHTMLResourceLoader {
  static $build;
  
  private static $_stylesheet_path = null;
  
  private static $_aio = null;
  private static $_fp_in = null;
  private static $_fp_out = null;
  
  /** 
   * IE Conditional comments
   * 
   * <!--[if IE]>Si IE<![endif]-->
   * <!--[if gte IE 5]> pour réserver le contenu à IE 5.0 et version plus récentes (actuellement E5.5, IE6.0 et IE7.0) <![endif]-->
   * <!--[if IE 5.0]> pour IE 5.0 <![endif]-->
   * <!--[if IE 5.5000]> pour IE 5.5 <![endif]-->
   * <!--[if IE 6]> pour IE 6.0 <![endif]-->
   * <!--[if gte IE 5.5000]> pour IE5.5 et supérieur <![endif]-->
   * <!--[if lt IE 6]> pour IE5.0 et IE5.5 <![endif]-->
   * <!--[if lt IE 7]> pour IE inférieur à IE7 <![endif]-->
   * <!--[if lte IE 6]> pour IE5.0, IE5.5 et IE6.0 mais pas IE7.0<![endif]-->
   * 
   * @param string $content Content to put between IE conditional comments
   * @param string $cc      The conditional comment
   * 
   * @return string The content inside conditional comments
   */
  static function conditionalComments($content, $cc) {
    if ($cc) {
      $content = "\n<!--[if $cc]>$content\n<![endif]-->";
    }
    return $content;
  }
  
  /** 
   * Returns the current app build, or the specified build
   * 
   * @param mixed $build [optional]
   * 
   * @return mixed The build
   */
  static function getBuild($build = null) {
    if (!$build) {
      $build = self::$build;
    }
    return $build;
  }
  
  /**
   * Returns the hash of a string
   * 
   * @param string $string The string to hash
   * 
   * @return string The hashed string
   */
  static function getHash($string) {
    return dechex(crc32($string));
  }
  
  /**
   * Gets the last change of a file
   * 
   * @param string $file The file of which we want the last change
   * 
   * @return int The timestamp of the last change of the file
   */
  static function getLastChange($file) {
    $stat_cache = stat($file);
    return max($stat_cache[9], $stat_cache[10]);
  }
  
  /**
   * Returns an HTML tag
   * 
   * @param string  $tagName    The tag name
   * @param array   $attributes [optional]
   * @param string  $content    [optional]
   * @param boolean $short      [optional]
   * 
   * @return string The HTML source code
   */
  static function getTag($tagName, $attributes = array(), $content = "", $short = true) {
    $tag = "<$tagName";
    foreach ($attributes as $key => $value) {
      $tag .= " $key=\"".htmlentities($value).'"';
    }
    if ($content != "") {
      $tag .= ">$content</$tagName>";
    }
    else {
      if ($short)
        $tag .= " />";
      else
        $tag .= "></$tagName>";
    }
    
    return $tag;
  }
  
  /**
   * Initialize output handler
   * 
   * @param bool $aio Embed everything inside a single file
   * 
   * @return void
   */
  static function initOutput($aio){
    self::$_aio = !!$aio;
    
    if (self::$_aio) {
      self::$_fp_in = CMbPath::getTempFile();
      ob_start(array("CHTMLResourceLoader", "outputToFile"), 8192);
    }
    else {
      ob_start();
    }
  }
  
  /**
   * Output the content to the standard output
   * 
   * @return void
   */
  static function output(){
    if (self::$_aio) {
      self::allInOne();
    }
    else {
      ob_end_flush();
    }
  }
  
  /**
   * Write a string to the cache file
   * 
   * @param string $str   The string to write
   * @param int    $flags Flags (not used)
   * 
   * @return string An empty string
   */
  static function outputToFile($str, $flags) {
    fwrite(self::$_fp_in, $str);
    return "";
  }
  
  /**
   * Get real memory peak usage or its placeholder
   * 
   * @param bool $real Return the real memory peak usage or the placeholder
   * 
   * @return string A formatted memory peak usage view 
   */
  static function getOutputMemory($real = false){
    if ($real || !self::$_aio) {
      return CMbString::toDecaBinary(memory_get_peak_usage(true));
    }
    
    return "[[AIO-memory]]";
  }
  
  /**
   * Get output length or its placeholder
   * 
   * @return string A formatted output length view 
   */
  static function getOutputLength(){
    if (!self::$_aio) {
      return CMbString::toDecaBinary(ob_get_length());
    }
    
    return "[[AIO-length]]";
  }
  
  /**
   * Get the content of a file
   * 
   * @param string $filename The name of the file
   * 
   * @return string The content of the file
   */
  private static function getFileContents($filename) {
    if (file_exists($filename)) {
      return file_get_contents($filename);
    }
  }
  
  /**
   * Replace <script> src attribute with the contents of the script
   * 
   * @param array $matches The matches of the regular expression
   * 
   * @return string The <script> tag
   */
  private static function replaceScriptSrc($matches) {
    $src = $matches[1];
    $src = preg_replace('/(\?.*)$/', '', $src);
    $script = self::getFileContents($src);
    return '<script type="text/javascript">'.$script.'</script>';
  }
  
  /**
   * Replace <img> src attribute with the contents of the image (base 64 encoded)
   * 
   * @param array $matches The matches of the regular expression
   * 
   * @return string The <img> tag
   */
  private static function replaceImgSrc($matches) {
    $src = $matches[2];
    $src = preg_replace('/(\?.*)$/', '', $src);
    
    if ($src) {
      if ($src[0] == "/") {
        $src = $_SERVER['DOCUMENT_ROOT'] . $src;
      }
      
      $ext = CMbPath::getExtension($src);
      $mime = "image/$ext";
      $img = self::getFileContents($src);
    }
    
    // Url avec des arguments (phpthumb par exemple)
    else {
      return null;
      
      /* // Ne fonctionne pas bien
      $session_name = CAppUI::$instance->session_name;
      $session_id = session_id();
      $src = CApp::getBaseUrl()."/".$matches[2];
      $context = stream_context_create(array(
        "http" => array(
          "method" => "GET",
          "header" => "Cookie: $session_name=$session_id\r\n"
        )
      ));

      
      $mime = "image/png";
      $img = file_get_contents($src, false, $context);
      
      foreach($http_response_header as $header) {
        if (preg_match("/^Content-Type: ([a-z\/]+)/", $header, $matches)) {
          $mime = $matches[1];
          break;
        }
      }*/
    }
    
    $matches[3] = rtrim($matches[3], " /");
    $img = " src=\"data:$mime;base64,".base64_encode($img)."\" ";
    return '<img '.$matches[1].$img.$matches[3].' />';
  }
  
  /**
   * Get the contents of a stylsheet
   * 
   * @param array $matches The matches of the regular expression
   * 
   * @return string The stylesheet contents
   */
  private static function replaceStylesheetImport($matches) {
    return self::getFileContents(self::$_stylesheet_path."/".$matches[1]);
  }
  
  /**
   * Replace the url(...) in a stylsheet with the base 64 encoded content of the stylesheet
   * 
   * @return string The url(...) string
   */
  private static function replaceStylesheetUrl($matches) {
    $src = $matches[1];
    
    if (strpos($src, "data:") === 0) {
      return $src;
    }
    
    $src = preg_replace('/(\?.*)$/', '', $src);
    $ext = CMbPath::getExtension($src);
    $url = self::getFileContents(self::$_stylesheet_path."/".$src);
    return "url(data:image/$ext;base64,".base64_encode($url).")";
  }
  
  /**
   * Embed the stylesheets' content
   * 
   * @param array $matches The matches of the regular expression
   * 
   * @return string The <style> tag
   */
  private static function replaceStylesheet($matches) {
    $src = $matches[1];
    $src = preg_replace('/(\?.*)$/', '', $src);
    $stylesheet = self::getFileContents($src);
    
    self::$_stylesheet_path = dirname($src);
    
    // @import
    $re = "/\@import\s+(?:url\()?[\"']?([^\"\'\)]+)[\"']?\)?;/i";
    $stylesheet = preg_replace_callback($re, array('self', 'replaceStylesheetImport'), $stylesheet);
    
    // url(foo)
    $re = "/url\([\"']?([^\"\'\)]+)[\"']?\)?/i";
    $stylesheet = preg_replace_callback($re, array('self', 'replaceStylesheetUrl'), $stylesheet);
    
    return '<style type="text/css">'.$stylesheet.'</style>';
  }
  
  /**
   * Embed all the external resources of the current output buffer inside a single file and outputs it.
   * 
   * @return void
   */
  private static function allInOne() {
    set_min_memory_limit("256M");
    
    self::$_fp_out = CMbPath::getTempFile();
    
    // End Output Buffering
    ob_end_clean();
    
    rewind(self::$_fp_in);
    while (!feof(self::$_fp_in)) {
      $line = fgets(self::$_fp_in);
      
      $line = preg_replace_callback("/<img([^>]*)src\s*=\s*[\"']([^\"']+)[\"']([^>]*)>/i", array('self', 'replaceImgSrc'), $line);
      $line = preg_replace_callback("/<link[^>]*rel=\"stylesheet\"[^>]*href\s*=\s*[\"']([^\"']+)[\"'][^>]*>/i", array('self', 'replaceStylesheet'), $line);
      $line = preg_replace_callback("/<script[^>]*src\s*=\s*[\"']([^\"']+)[\"'][^>]*>\s*<\/script>/i", array('self', 'replaceScriptSrc'), $line);
      
      fwrite(self::$_fp_out, $line);
    }
    
    $length = 0;
    rewind(self::$_fp_out);
    while (!feof(self::$_fp_out)) {
      $str = fread(self::$_fp_out, 4096);
      $length += strlen($str);
      
      $str = str_replace("[[AIO-length]]", CMbString::toDecaBinary($length), $str);
      
      if (strpos($str, "[[AIO-memory]]") !== false) {
        $str = str_replace("[[AIO-memory]]", self::getOutputMemory(true), $str);
      }
      
      echo $str;
    }
  }
}

global $version;
CHTMLResourceLoader::$build = $version["build"];
