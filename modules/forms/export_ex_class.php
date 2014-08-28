<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$ex_class_id  = CValue::get("ex_class_id");

$ex_class = new CExClass();
$ex_class->load($ex_class_id);

try {
  $backrefs_tree = array(
    "CExClass" => array(
      "field_groups",
    ),
    
    "CExClassFieldGroup" => array(
      "class_fields",
      "host_fields",
      "class_messages",
      "subgroups",
    ),

    "CExClassField" => array(
      "field_translations",
      "list_items",
      "properties",
      "predicates",
    ),

    "CExList" => array(
      "list_items",
    ),

    "CExClassMessage" => array(
      "properties",
    ),

    "CExClassFieldPredicate" => array(
      "properties",
    ),
    
    "CExClassFieldProperties" => array(
      
    )
  );
  
  $fwdrefs_tree = array(
    "CExClassFieldGroup" => array(
      "ex_class_id",
    ),
    "CExClassField" => array(
      "ex_group_id",
      "concept_id",
      "predicate_id",
    ),
    "CExClassMessage" => array(
      "ex_group_id",
      "subgroup_id",
      "predicate_id",
    ),
    "CExClassHostField" => array(
      "ex_group_id",
    ),
    "CExClassFieldTranslation" => array(
      "ex_class_field_id",
    ),
    
    "CExConcept" => array(
      "ex_list_id",
    ),

    "CExListItem" => array(
      "list_id",
      "concept_id",
      "field_id",
    ),
    
    "CExClassFieldProperty" => array(
      "predicate_id",
      "object_id",
    ),

    "CExClassFieldPredicate" => array(
      "ex_class_field_id",
    ),
    
    "CExClassFieldSubroup" => array(
      "predicate_id",
    ),
  );
  
  $export = new CMbObjectExport($ex_class, $backrefs_tree);
  
  $export->setForwardRefsTree($fwdrefs_tree);
}
catch (CMbException $e) {
  $e->stepAjax(UI_MSG_ERROR);
}

$export->streamXML();
