<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("resourceLoaders/CHTMLResourceLoader");

abstract class CFaviconLoader extends CHTMLResourceLoader {
  
  /**
   * Links a shortcut icon (aka "favicon")
   * Only to be called while in the HTML header
   */
  static function loadFile($file) {
    if (CAppUI::pref("moduleFavicon")) {
      global $m;
      
      $file = "./modules/$m/images/icon.png";
      return self::getTag("link", array(
        "type" => "image/png",
        "rel"  => "shortcut icon",
        "href" => "$file?".self::getBuild(),
      ));
    }

    return self::getTag("link", array(
      "type" => "image/ico",
      "rel"  => "shortcut icon",
      "href" => "$file?".self::getBuild(),
    ));
  }
}
