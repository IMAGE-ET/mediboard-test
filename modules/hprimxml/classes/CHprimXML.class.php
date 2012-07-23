<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 12588 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHprimXML {
  static $object_handlers = array(
    "CSipObjectHandler"     => "CSipHprimXMLObjectHandler",
    "CSmpObjectHandler"     => "CSmpHprimXMLObjectHandler",
    "CSaObjectHandler"      => "CSaHprimXMLObjectHandler",
    "CSaEventObjectHandler" => "CSaEventHprimXMLObjectHandler"
  );
  
  static function getObjectHandlers() {
    return self::$object_handlers; 
  }
  
  
  static function getDefaultTag($group_id = null) {
    // Pas de tag hprimxml
    if (null == $tag_hprimxml = CAppUI::conf("hprimxml tag_default")) {
      return;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag_hprimxml);
  } 
}

?>