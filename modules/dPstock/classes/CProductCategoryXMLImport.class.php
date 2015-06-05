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
class CProductCategoryXMLImport extends CMbXMLObjectImport {
  protected $imported = array();

  protected $import_order = array(
    // Structure objects
    "//object[@class='CGroups']",
    "//object[@class='CSociete']",
    "//object[@class='CProductCategory']",
    "//object[@class='CProductStockLocation']",
    "//object[@class='CProduct']",
    "//object[@class='CProductStockGroup']",
    "//object[@class='CProductStockService']",
    "//object",
  );

  static $_ignored_classes = array("CGroups", "CService");

  /**
   * @param DOMElement $element
   * @param string     $class
   * @param array      $fields
   * @param string     $msg_found
   * @param string     $msg_created
   *
   * @return CMbObject
   * @throws Exception
   */
  protected function findOrCreate(DOMElement $element, $class, $fields, $msg_found, $msg_created) {
    /** @var CMbObject $_object */
    $_object = $this->getObjectFromElement($element);

    /** @var CMbObject $_similar */
    $_similar = new $class();
    foreach ($fields as $_field) {
      $_similar->$_field = $_object->$_field;
    }

    if (!$_similar->loadMatchingObject()) {
      if ($msg = $_object->store()) {
        CAppUI::stepAjax($msg, UI_MSG_WARNING);
        throw new Exception($msg);
      }

      CAppUI::stepAjax($msg_created, UI_MSG_OK, $_object->_view);
    }
    else {
      $_object = $_similar;
      CAppUI::stepAjax($msg_found, UI_MSG_OK, $_object->_view);
    }

    return $_object;
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
    $imported_object = null;

    $idex = self::lookupObject($id);
    if ($idex->_id) {
      CAppUI::stepAjax("'$id' présent en base", UI_MSG_OK);
      $this->imported[$id] = true;
      $this->map[$id] = $idex->loadTargetObject()->_guid;
      return;
    }

    switch ($_class) {
      case "CSociete":
        /** @var CSociete $_object */
        $_object = $this->getObjectFromElement($element);

        if (!$_object->loadMatchingObject()) {
          if ($msg = $_object->store()) {
            CAppUI::stepAjax($msg, UI_MSG_WARNING);
            throw new Exception($msg);
          }

          CAppUI::stepAjax("Société '%s' créée", UI_MSG_OK, $_object->_view);
        }
        else {
          CAppUI::stepAjax("Société '%s' retrouvée", UI_MSG_OK, $_object->_view);
        }

        $imported_object = $_object;
        break;

      case "CProduct":
        $imported_object = $this->findOrCreate(
          $element,
          $_class,
          array("code_canonical"),
          "Société '%s' retrouvée",
          "Société '%s' créée"
        );
        break;

      case "CProductReference":
        $imported_object = $this->findOrCreate(
          $element,
          $_class,
          array("quantity", "societe_id", "product_id"),
          "Référence '%s' retrouvée",
          "Référence '%s' créée"
        );
        break;

      case "CProductStockGroup":
        $imported_object = $this->findOrCreate(
          $element,
          $_class,
          array("product_id", "group_id"),
          "Stock établissement '%s' retrouvé",
          "Stock établissement '%s' créé"
        );
        break;

      case "CProductStockService":
        $imported_object = $this->findOrCreate(
          $element,
          $_class,
          array("object_id", "object_class", "product_id"),
          "Stock service '%s' retrouvé",
          "Stock service '%s' créé"
        );
        break;

      default:
        // Ignored classes
        if (in_array($_class, self::$_ignored_classes)) {
          break;
        }

        $_object = $this->getObjectFromElement($element);

        if ($msg = $_object->store()) {
          CAppUI::stepAjax($msg, UI_MSG_WARNING);
          break;
        }
        CAppUI::stepAjax(CAppUI::tr($_object->_class) . " '%s' créé", UI_MSG_OK, $_object);

        $imported_object = $_object;
        break;
    }

    // Store idex on new object
    if ($imported_object) {
      $idex->setObject($imported_object);
      $idex->id400 = $id;
      if ($msg = $idex->store()) {
        CAppUI::stepAjax($msg, UI_MSG_WARNING);
      }
    }
    else {
      if (!in_array($_class, self::$_ignored_classes)) {
        CAppUI::stepAjax("$id sans objet", UI_MSG_WARNING);
      }
    }

    if ($imported_object) {
      $this->map[$id] = $imported_object->_guid;
    }

    $this->imported[$id] = true;
  }


  /**
   * @see parent::importObjectByGuid()
   */
  function importObjectByGuid($guid) {
    list($class, $id) = explode("-", $guid);

    if (in_array($class, self::$_ignored_classes)) {
      $lookup_guid = $guid;

      $idex = $this->lookupObject($lookup_guid);

      if ($idex->_id) {
        $this->map[$guid] = "$class-$idex->object_id";
        $this->imported[$guid] = true;
      }
    }
    else {
      /** @var DOMElement $_element */
      $_element = $this->xpath->query("//*[@id='$guid']")->item(0);
      $this->importObject($_element);
    }
  }

  /**
   * Lookup an object already imported
   *
   * @param string $guid Guid of the object to lookup
   * @param string $tag  Tag of it's Idex
   *
   * @return CIdSante400
   */
  function lookupObject($guid, $tag = "migration") {
    list($class, $id) = explode("-", $guid);

    $idex = CIdSante400::getMatch($class, $tag, null, $id);

    return $idex;
  }
}