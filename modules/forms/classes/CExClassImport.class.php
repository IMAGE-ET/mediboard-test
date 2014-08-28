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

class CExClassImport extends CMbXMLObjectImport {
  protected $name_suffix;
  
  protected $imported = array();
  
  function importObject(DOMElement $element) {
    $id = $element->getAttribute("id");
    
    if (isset($this->imported[$id])) {
      return;
    }
    
    $this->name_suffix = " (import du ".CMbDT::dateTime().")";
    
    $map_to = isset($this->map[$id]) ? $this->map[$id] : null;
    
    switch ($element->getAttribute("class")) {
      // --------------------
      case "CExClass":
        $ex_class = new CExClass();
        $ex_class->name = $this->options["ex_class_name"];
        $ex_class->group_id = CGroups::loadCurrent()->_id;
        $ex_class->_dont_create_default_group = true;

        if ($msg = $ex_class->store()) {
          CAppUI::stepAjax($msg, UI_MSG_WARNING);
          return;
        }

        CAppUI::stepAjax("Formulaire '%s' créé", UI_MSG_OK, $ex_class->name);
        
        $map_to = $ex_class->_guid;
        break;

      // --------------------
      case "CExList":
        if ($map_to == "__create__") {
          /** @var CExList $_ex_list */
          $_ex_list = $this->getObjectFromElement($element);
          $_ex_list->name .= $this->name_suffix;
          
          if ($msg = $_ex_list->store()) {
            CAppUI::stepAjax($msg, UI_MSG_WARNING);
            break;
          }
          CAppUI::stepAjax("Liste '%s' créée", UI_MSG_OK, $_ex_list);

          $_elements = $this->getElementsByFwdRef("CExListItem", "list_id", $id);
          foreach ($_elements as $_element) {
            $_list_item = new CExListItem();
            bindHashToObject($this->getValuesFromElement($_element), $_list_item);
            $_list_item->list_id = $_ex_list->_id;

            if ($msg = $_list_item->store()) {
              CAppUI::stepAjax($msg, UI_MSG_WARNING);
              break;
            }
            CAppUI::stepAjax("Elément de liste '%s' créé", UI_MSG_OK, $_list_item);
            $this->imported[$_element->getAttribute("id")] = true;
          }

          $map_to = $_ex_list->_guid;
        }
        break;

      // --------------------
      case "CExConcept":
        if ($map_to == "__create__") {
          /** @var CExConcept $_ex_concept */
          $_ex_concept = $this->getObjectFromElement($element);
          $_ex_concept->name .= $this->name_suffix;

          if ($_ex_concept->ex_list_id) {
            $_ex_concept->updatePropFromList();
          }

          $_ex_concept->prop = $_ex_concept->updateFieldProp($_ex_concept->prop);

          if ($msg = $_ex_concept->store()) {
            CAppUI::stepAjax($msg, UI_MSG_WARNING);
            break;
          }
          CAppUI::stepAjax("Concept '%s' créé", UI_MSG_OK, $_ex_concept);

          $map_to = $_ex_concept->_guid;
        }
        break;
      
      case "CExClassField":
        /** @var CExClassField $_ex_field */
        $_ex_field = $this->getObjectFromElement($element);
        if ($this->options["ignore_disabled_fields"] && $_ex_field->disabled) {
          break;
        }

        if ($msg = $_ex_field->store()) {
          CAppUI::stepAjax($msg, UI_MSG_WARNING);
          break;
        }
        CAppUI::stepAjax("Champ '%s' créé", UI_MSG_OK, $_ex_field);

        $map_to = $_ex_field->_guid;
        break;

      // --------------------
      case "CExClassFieldGroup":
      case "CExClassFieldTranslation":
      case "CExClassMessage":
      case "CExClassHostField":
        $_object = $this->getObjectFromElement($element);

        if ($msg = $_object->store()) {
          CAppUI::stepAjax($msg, UI_MSG_WARNING);
          break;
        }
        CAppUI::stepAjax(CAppUI::tr($_object->_class)." '%s' créé", UI_MSG_OK, $_object);

        $map_to = $_object->_guid;
        break;
      
      default:
        // Ignore object
        break;
    }
    
    $this->map[$id] = $map_to;
    
    $this->imported[$id] = true;
  }
}