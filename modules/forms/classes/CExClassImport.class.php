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
  
  /** @var CExClassFieldPredicate[] */
  protected $predicates_to_fix = array();
  
  protected $import_order = array(
    "//object[@class='CExClass']",
    "//object[@class='CExList']",
    "//object[@class='CExConcept']",
    "//object",
  );

  /**
   * @see parent::afterImport()
   */
  function afterImport(){
    foreach ($this->predicates_to_fix as $_predicate) {
      $_predicate->value = $this->getIdFromGuid($this->map["CExListItem-".$_predicate->value]);
      if ($msg = $_predicate->store()) {
        CAppUI::stepAjax($msg, UI_MSG_WARNING);
      }
    }
  }
  
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
        $values = self::getValuesFromElement($element);

        $ex_class = new CExClass();
        $ex_class->name                       = $this->options["ex_class_name"];
        $ex_class->group_id                   = CGroups::loadCurrent()->_id;
        $ex_class->pixel_positionning         = $values["pixel_positionning"];
        $ex_class->native_views               = $values["native_views"];
        $ex_class->_dont_create_default_group = true;

        if ($msg = $ex_class->store()) {
          throw new Exception($msg);
        }

        CAppUI::stepAjax("Formulaire '%s' créé", UI_MSG_OK, $ex_class->name);
        
        $map_to = $ex_class->_guid;
        break;

      // --------------------
      case "CExList":
        if ($map_to == "__create__") {
          /** @var CExList $_ex_list */
          $_ex_list = $this->getObjectFromElement($element);
          
          if ($msg = $_ex_list->store()) {
            $_ex_list->name .= $this->name_suffix;
          }
          
          if ($msg = $_ex_list->store()) {
            CAppUI::stepAjax($msg, UI_MSG_WARNING);
            break;
          }
          CAppUI::stepAjax("Liste '%s' créée", UI_MSG_OK, $_ex_list);

          $_elements = $this->getElementsByFwdRef("CExListItem", "list_id", $id);
          foreach ($_elements as $_element) {
            $_list_item = new CExListItem();
            bindHashToObject(self::getValuesFromElement($_element), $_list_item);
            $_list_item->list_id = $_ex_list->_id;

            if ($msg = $_list_item->store()) {
              CAppUI::stepAjax($msg, UI_MSG_WARNING);
              break;
            }
            CAppUI::stepAjax("Elément de liste '%s' créé", UI_MSG_OK, $_list_item);

            $_item_id = $_element->getAttribute("id");

            $this->map[$_item_id] = $_list_item->_guid;
            $this->imported[$_item_id] = true;
          }

          $map_to = $_ex_list->_guid;
        }
        else {
          /** @var CExList $ex_list */
          $ex_list = CStoredObject::loadFromGuid($map_to);
          $list_items = $ex_list->loadRefItems();
          foreach ($list_items as $_item) {
            $this->map[$_item->_guid] = $_item->_guid;
          }
        }
        break;

      // --------------------
      case "CExConcept":
        if ($map_to == "__create__") {
          /** @var CExConcept $_ex_concept */
          $_ex_concept = $this->getObjectFromElement($element);

          if ($_ex_concept->ex_list_id) {
            $_ex_concept->updatePropFromList();
          }

          $_ex_concept->prop = $_ex_concept->updateFieldProp($_ex_concept->prop);

          if ($msg = $_ex_concept->store()) {
            $_ex_concept->name .= $this->name_suffix;
          }
          
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
        $_ex_field->_make_unique_name = false;
        
        // Met à jour default|XXX des champs enum pour garder la bonne référence
        // @FIXME Ne fonctionne pas à cause du fait qu'il y a un concept_id ....
        $_spec_obj = $_ex_field->getSpecObject();
        if ($_spec_obj instanceof CEnumSpec && $_spec_obj->default) {
          $_new_default = $this->getIdFromGuid($this->map["CExListItem-$_spec_obj->default"]);
          $_ex_field->prop = preg_replace('/ default\|\d+/', " default|$_new_default", $_ex_field->prop);
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
      case "CExClassFieldSubgroup":
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
      
      case "CExClassFieldPredicate":
        /** @var CExClassFieldPredicate $_object */
        $_object = $this->getObjectFromElement($element);
        
        if ($_object->value) {
          $_field = $_object->loadRefExClassField();
          if ($_field->getSpecObject() instanceof CEnumSpec) {
            $this->predicates_to_fix[] = $_object;
          }
        }

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