<?php

/**
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Domain handler
 */
class CEAIGroupsHandler extends CMbObjectHandler {

  static $handled = array("CGroups");

  public $create = false;

  /**
   * @see parent::isHandled()
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * @see parent::onBeforeStore()
   */
  function onBeforeStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    if (!$mbObject->_id) {
      $this->create = true;
    }

    return true;
  }

  /**
   * @see parent::onAfterStore()
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    if (!$mbObject->_id || !$this->create) {
      return false;
    }

    $group_id     = $mbObject->_id;
    $object_class = array("CSejour", "CPatient");
    global $dPconfig;
    $original_value = $dPconfig["eai"]["use_domain"];
    $dPconfig["eai"]["use_domain"] = "0";

    foreach ($object_class as $_class) {
      switch ($_class) {
        case "CSejour":
          $tag_group = CSejour::getTagNDA($group_id);
          break;
        case "CPatient":
          $tag_group = CPatient::getTagIPP($group_id);
          break;
        default:
          $tag_group = null;
      }

      if (!$tag_group) {
        continue;
      }

      $domain = new CDomain();
      $domain->tag = $tag_group;
      if ($domain->store()) {
        continue;
      }

      $group_domain               = new CGroupDomain();
      $group_domain->group_id     = $group_id;
      $group_domain->domain_id    = $domain->_id;
      $group_domain->object_class = $_class;
      $group_domain->master       = "1";
      $group_domain->store();
    }

    $dPconfig["eai"]["use_domain"] = "$original_value";

    return true;
  }
}
