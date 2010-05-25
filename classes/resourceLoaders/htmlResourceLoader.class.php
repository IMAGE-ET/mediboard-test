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
}

global $version;
CHTMLResourceLoader::$build = $version["build"];
