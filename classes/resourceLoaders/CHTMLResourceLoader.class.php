<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

abstract class CHTMLResourceLoader {
  static $build;
  private static $_stylesheet_path = null;
  
  /** 
   * IE Conditional comments
   * <!--[if IE]>Si IE<![endif]-->
   * <!--[if gte IE 5]> pour réserver le contenu à IE 5.0 et version plus récentes (actuellement E5.5, IE6.0 et IE7.0) <![endif]-->
   * <!--[if IE 5.0]> pour IE 5.0 <![endif]-->
   * <!--[if IE 5.5000]> pour IE 5.5 <![endif]-->
   * <!--[if IE 6]> pour IE 6.0 <![endif]-->
   * <!--[if gte IE 5.5000]> pour IE5.5 et supérieur <![endif]-->
   * <!--[if lt IE 6]> pour IE5.0 et IE5.5 <![endif]-->
   * <!--[if lt IE 7]> pour IE inférieur à IE7 <![endif]-->
   * <!--[if lte IE 6]> pour IE5.0, IE5.5 et IE6.0 mais pas IE7.0<![endif]-->
   */
  static function conditionalComments($content, $cc) {
    if ($cc) {
      $content = "\n<!--[if $cc]>$content\n<![endif]-->";
    }
    return $content;
  }
  
  /** 
   * Returns the current app build, or the specified build
   * @param mixed $build [optional]
   * @return mixed The build
   */
  static function getBuild($build = null) {
    if (!$build) {
      $build = self::$build;
    }
    return $build;
  }
  
  static function getHash($string) {
    return dechex(crc32($string));
  }
  
  static function getLastChange($file) {
    $stat_cache = stat($file);
    return max($stat_cache[9], $stat_cache[10]);
  }
  
  /**
   * Returns an HTML tag
   * @param string $tagName The tag name
   * @param array $attributes [optional]
   * @param string $content [optional]
   * @param boolean $short [optional]
   * @return 
   */
  static function getTag($tagName, $attributes = array(), $content = "", $short = true) {
    $tag = "<$tagName";
    foreach($attributes as $key => $value) {
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
  
  private static function getFileContents($filename) {
    if (file_exists($filename)) 
      return file_get_contents($filename);
  }
  
  private static function replaceScriptSrc($matches) {
    $src = $matches[1];
    $src = preg_replace('/(\?.*)$/', '', $src);
    $script = self::getFileContents($src);
    return '<script type="text/javascript">'.$script.'</script>';
  }
  
  private static function replaceImgSrc($matches) {
    $src = $matches[2];
    $src = preg_replace('/(\?.*)$/', '', $src);
    if ($src[0] == "/")
      $src = $_SERVER['DOCUMENT_ROOT'] . $src;
    $ext = CMbPath::getExtension($src);
    $img = self::getFileContents($src);
    $img = " src=\"data:image/$ext;base64,".base64_encode($img)."\" ";
    return '<img '.$matches[1].$img.$matches[3].' />';
  }
  
  private static function replaceStylesheetImport($matches) {
    return self::getFileContents(self::$_stylesheet_path."/".$matches[1]);
  }
  
  private static function replaceStylesheetUrl($matches) {
    $src = $matches[1];
    $src = preg_replace('/(\?.*)$/', '', $src);
    $ext = CMbPath::getExtension($src);
    $url = self::getFileContents(self::$_stylesheet_path."/".$src);
    return "url(data:image/$ext;base64,".base64_encode($url).")";
  }
  
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
  
  protected static function getSectionRegexp($name, $match_all = true) {
    if ($match_all) {
      return "@(\<\!--\[$name\]--\>.*\<\!--\[/$name\]--\>)@s";
    }
    
    return "@\<\!--\[$name\]--\>(.*)\<\!--\[/$name\]--\>@s";
  }
  
  protected static function getSectionString($name) {
    return "<!--$name-->";
  }
  
  protected static function embed($html) {
    $html = preg_replace_callback("/<img([^>]*)src\s*=\s*[\"']([^\"']+)[\"']([^>]*)>/i", array('self', 'replaceImgSrc'), $html);
    $html = preg_replace_callback("/<link[^>]*rel=\"stylesheet\"[^>]*href\s*=\s*[\"']([^\"']+)[\"'][^>]*>/i", array('self', 'replaceStylesheet'), $html);
    $html = preg_replace_callback("/<script[^>]*src\s*=\s*[\"']([^\"']+)[\"'][^>]*>\s*<\/script>/i", array('self', 'replaceScriptSrc'), $html);
    return $html;
  }
  
  protected static function embedSections($html, $sections) {
    $sections_embedded = array();
    
    foreach($sections as $name => $shm) {
      $source = SHM::get("aio-$shm");
      
      if (!$source) {
        preg_match(self::getSectionRegexp($name, false), $html, $matches);
        $orig_source = $matches[1];
        $source = self::embed($orig_source);
        SHM::put("aio-$shm", $source);
      }
      
      $html = preg_replace(self::getSectionRegexp($name, true), self::getSectionString($name), $html);
      
      $sections_embedded[$name] = $source;
    }
    
    $html = self::embed($html);
    
    
    foreach($sections_embedded as $name => $source) {
      $html = str_replace(self::getSectionString($name), $source, $html);
    }

    return $html;
  }
  
  static function allInOne($html) {
  	set_min_memory_limit("256M");
    $html = preg_replace_callback("/<img([^>]*)src\s*=\s*[\"']([^\"']+)[\"']([^>]*)>/i", array('self', 'replaceImgSrc'), $html);
    $html = preg_replace_callback("/<link[^>]*rel=\"stylesheet\"[^>]*href\s*=\s*[\"']([^\"']+)[\"'][^>]*>/i", array('self', 'replaceStylesheet'), $html);
    $html = preg_replace_callback("/<script[^>]*src\s*=\s*[\"']([^\"']+)[\"'][^>]*>\s*<\/script>/i", array('self', 'replaceScriptSrc'), $html);
    return $html;
  }
  
  /*
  static function allInOne($html, $shm = true) {
    set_min_memory_limit("256M");
    
    if ($shm) {
      $html = self::embedSections($html, array(
        "HEAD_CSS" => "css-".CAppUI::pref("UISTYLE"),
        "HEAD_JS"  => "js",
      ));
    }
    else {
      $html = self::embed($html);
    }
    
    return $html;
  }*/
}

global $version;
CHTMLResourceLoader::$build = $version["build"];
