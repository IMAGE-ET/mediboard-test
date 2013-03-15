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
   * Get default tag
   *
   * @param int $group_id Group
   *
   * @return null|string
   */
  static function getDefaultTag($group_id = null) {
    // Pas de tag hl7
    if (null == $tag_hl7 = CAppUI::conf("hl7 tag_default")) {
      return null;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag_hl7);
  } 
}

CHL7::$versions = array (
  "v2" => CHL7v2::$versions,
);
