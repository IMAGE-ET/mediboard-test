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
  static function loadFile($file, $media = null, $cc = null) {
    $tag = self::getTag("link", array(
      "type"  => "text/css",
      "rel"   => "stylesheet",
      "href"  => "$file?".self::getBuild(),
      "media" => $media,
    ));
    
    return self::conditionalComments($tag, $cc);
  }
}
