<?php 

/**
 * $Id$
 *  
 * @category Forms
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * CGroups import import class
 */
class CGroupsImport extends CMbXMLObjectImport {
  protected $name_suffix;
  
  protected $imported = array();
  
  protected $import_order = array(
    "//object[@class='CGroups']",
    "//object[@class='CService']",
    "//object[@class='CFunctions']",
    "//object[@class='CUser']",
    "//object[@class='CBlocOperatoire']",
    "//object[@class='CSalle']",
    "//object",
  );

  /**
   * @see parent::storeIdExt()
   */
  protected function storeIdExt(CMbObject $object, $map_to) {
    if ($object instanceof CIdSante400) {
      return;
    }

    // Rattachement d'un ID externe
    $idex = CIdSante400::getMatchFor($object, "migration");
    if (!$idex->_id) {
      $idex->id400 = $map_to;
      $idex->last_update = CMbDT::dateTime();
      $idex->store();
      CAppUI::stepAjax("Idex '%s' créé sur '%s'", UI_MSG_OK, $idex->id400, $object);
    }
  }

  /**
   * @see parent::importObject()
   */
  function importObject(DOMElement $element) {
    $id = $element->getAttribute("id");

    if (isset($this->imported[$id])) {
      return;
    }

    $_class = $element->getAttribute("class");
    $group = CGroups::loadCurrent();


    if ($_class == "CGroups") {
      $this->storeIdExt($group, $id);

      $this->map[$id] = $group->_guid;

      $this->imported[$id] = true;
    }
    
    $this->name_suffix = " (import du ".CMbDT::dateTime().")";
    
    $map_to = isset($this->map[$id]) ? $this->map[$id] : null;

    if (!$map_to) {
      //CAppUI::stepAjax("ID ignoré : '%s'", UI_MSG_OK, $id);
      return;
    }
    
    switch ($_class) {
      // --------------------
      case "CGroups":
        CAppUI::stepAjax("Etablissement de rattachement : '%s'", UI_MSG_OK, $group->text);

        $map_to = $group->_guid;
        break;

      case "CUser":
        switch ($map_to) {
          case "__ignore__":
            // Ignore
            break;

          default:
            /** @var CMbObject $_object */
            $_object = CStoredObject::loadFromGuid($map_to);

            $this->storeIdExt($_object, $map_to);

            $map_to = $_object->_guid;
        }
        break;
      
      case "CSalle":
      case "CFunctions":
      case "CService":
      case "CBlocOperatoire":
        switch ($map_to) {
          case "__ignore__":
            // Ignore
            break;

          case "__create__":
            /** @var CSalle|CFunctions|CService|CBlocOperatoire $_object */
            $_object = $this->getObjectFromElement($element);

            if ($msg = $_object->store()) {
              if (
                  $_object instanceof CGroups ||
                  $_object instanceof CFunctions
              ) {
                $_object->text .= $this->name_suffix;
              }
              elseif (
                  $_object instanceof CSalle ||
                  $_object instanceof CService ||
                  $_object instanceof CBlocOperatoire
              ) {
                $_object->nom .= $this->name_suffix;
              }
            }

            if ($msg = $_object->store()) {
              CAppUI::stepAjax($msg, UI_MSG_WARNING);
              break;
            }
            CAppUI::stepAjax("%s '%s' créé", UI_MSG_OK, CAppUI::tr($_object->_class), $_object);

            $map_to = $_object->_guid;
            break;

          default:
            /** @var CMbObject $_object */
            $_object = CStoredObject::loadFromGuid($map_to);

            $this->storeIdExt($_object, $map_to);

            $map_to = $_object->_guid;
        }
        break;
      
      default:
        // Ignore object
        break;
    }
    
    $this->map[$id] = $map_to;
    
    $this->imported[$id] = true;
  }
}