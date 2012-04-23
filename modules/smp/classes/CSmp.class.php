<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSmp {
  /**
   * Construit le tag d'une venue en fonction des variables de configuration
   * @param $group_id Permet de charger l'id externe d'une venue pour un établissement donné si non null
   * @return string
   */
  static function getTagVisitNumber($group_id = null) {
    // Pas de tag venue
    if (null == $tag_visit_number = CAppUI::conf("smp tag_visit_number")) {
      return;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag_visit_number);
  } 
} 