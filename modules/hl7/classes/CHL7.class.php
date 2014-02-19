<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7 
 * Tools
 */
class CHL7 {
  static $versions = array ();

  /**
   * Get object tag
   *
   * @param string $group_id Group
   *
   * @return string|null
   */
  static function getObjectTag($group_id = null) {
    $context = array(get_called_class().":".__FUNCTION__, func_get_args());

    if (CFunctionCache::exist($context)) {
      return CFunctionCache::get($context);
    }

    $tag = self::getDynamicTag();

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    return CFunctionCache::set($context, str_replace('$g', $group_id, $tag));
  }

  /**
   * Get object dynamic tag
   *
   * @return string
   */
  static function getDynamicTag() {
    return CAppUI::conf("hl7 tag_default");
  }
}

CHL7::$versions = array (
  "v2" => CHL7v2::$versions,
);
