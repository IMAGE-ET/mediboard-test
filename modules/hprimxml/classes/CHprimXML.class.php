<?php

/**
 * H'XML
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CHprimXML
 */
class CHprimXML extends CMbObject {
  static $object_handlers = array(
    "CSipObjectHandler"     => "CSipHprimXMLObjectHandler",
    "CSmpObjectHandler"     => "CSmpHprimXMLObjectHandler",
    "CSaObjectHandler"      => "CSaHprimXMLObjectHandler",
    "CSaEventObjectHandler" => "CSaEventHprimXMLObjectHandler"
  );

  /**
   * Get object handlers
   *
   * @return array
   */
  static function getObjectHandlers() {
    return self::$object_handlers; 
  }

  /**
   * Get H'XML tag
   *
   * @param string $group_id Group
   *
   * @return mixed
   */
  static function getDefaultTag($group_id = null) {
    // Pas de tag hprimxml
    if (null == $tag_hprimxml = CAppUI::conf("hprimxml tag_default")) {
      return null;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag_hprimxml);
  }

  /**
   * @see parent::getDynamicTag
   */
  function getDynamicTag() {
    return $this->conf("tag_default");
  }
}