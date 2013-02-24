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
 * Favicon loader utility class
 */
abstract class CFaviconLoader extends CHTMLResourceLoader {

  /**
   * Links a shortcut icon (aka "favicon")
   * Only to be called while in the HTML header
   *
   * @param string $file The path to the favicon file
   * @param string $type The favicon mime type
   *
   * @return string An HTML tag to load the favicon
   */
  static function loadFile($file, $type = "image/ico") {
    if (CAppUI::pref("moduleFavicon")) {
      global $m;
      
      $file = "./modules/$m/images/icon.png";
      
      return self::getTag(
        "link", 
        array(
          "type" => "image/png",
          "rel"  => "shortcut icon",
          "href" => "$file?".self::getBuild(),
        )
      );
    }

    return self::getTag(
      "link", array(
        "type" => $type,
        "rel"  => "shortcut icon",
        "href" => "$file?".self::getBuild(),
      )
    );
  }
}
