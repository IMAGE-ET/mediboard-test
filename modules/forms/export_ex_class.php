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
      "enum_translations",
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
  );
  
  $fwdrefs_tree = array(
    "CExClassField" => array(
      "concept_id",
      "predicate_id",
    ),
    
    "CExConcept" => array(
      "ex_list_id",
    ),
    
    "CExClassFieldProperty" => array(
      "predicate_id",
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
