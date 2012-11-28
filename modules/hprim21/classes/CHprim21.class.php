<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: 10062 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


/**
 * Hprim 21 utility class
 */
class CHprim21 {  
  static function getTag($group_id = null) {
    // Pas de tag Identifiant
    if (null == $tag = CAppUI::conf("hprim21 tag")) {
      return;
    }

    // Permettre des ID en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag);
  }
}
