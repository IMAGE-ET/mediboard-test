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
 * Class CHPrimXML
 */
class CHPrimXML extends CInteropNorm {
  static $object_handlers = array(
    "CSipObjectHandler"     => "CSipHprimXMLObjectHandler",
    "CSmpObjectHandler"     => "CSmpHprimXMLObjectHandler",
    "CSaObjectHandler"      => "CSaHprimXMLObjectHandler",
    "CSaEventObjectHandler" => "CSaEventHprimXMLObjectHandler"
  );

  static $documentElements = array();

  /**
   * Get object handlers
   *
   * @return array
   */
  static function getObjectHandlers() {
    return self::$object_handlers;
  }

  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->name = "CHPrimXML";

    parent::__construct();
  }

  /**
   * Récupération des évènements disponibles
   *
   * @return array
   */
  function getDocumentElements() {
    return self::$documentElements;
  }

  /**
   * Récupération de l'évènement H'XML
   *
   * @return CHPrimXMLEvenements
   */
  static function getHPrimXMLEvenements() {
  }

  /**
   * Get object tag
   *
   * @param string $group_id Group
   *
   * @return string|null
   */
  static function getObjectTag($group_id = null) {
    // Recherche de l'établissement
    $group = CGroups::get($group_id);
    if (!$group_id) {
      $group_id = $group->_id;
    }

    $cache = new Cache(__METHOD__, array($group_id), Cache::INNER);

    if ($cache->exists()) {
      return $cache->get();
    }

    $tag = self::getDynamicTag();

    return $cache->put(str_replace('$g', $group_id, $tag));
  }

  /**
   * Get object dynamic tag
   *
   * @return string
   */
  static function getDynamicTag() {
    return CAppUI::conf("hprimxml tag_default");
  }
}