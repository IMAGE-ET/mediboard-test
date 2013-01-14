<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$file = CValue::read($_FILES, 'import');
$separator = CValue::post("separator", ',');
$enclosure = CValue::post("enclosure", '"');

/*

TRUNCATE `ex_list`;
TRUNCATE `ex_list_item`;
TRUNCATE `ex_concept`;
TRUNCATE `tag`;
TRUNCATE `tag_item`;

TRUNCATE `ex_class`;
TRUNCATE `ex_class_constraint`;
TRUNCATE `ex_class_field`;
TRUNCATE `ex_class_field_enum_translation`;
TRUNCATE `ex_class_field_group`;
TRUNCATE `ex_class_field_translation`;
TRUNCATE `ex_class_field_trigger`;
TRUNCATE `ex_class_host_field`;

DELETE FROM user_log WHERE object_class IN(
"CExList", "CExListItem", "CExConcept", "CTag", "CTagItem", "CExClass",
"CExClassConstraint", "CExClassField", "CExClassFieldEnumTranslation",
"CExClassFieldGroup", "CExClassFieldTranslation", "CExClassFieldTrigger", "CExClassHostField");

DELETE FROM user_log WHERE object_class LIKE "CExObject%";

DROP TABLE `ex_object_1`, `ex_object_2`, `ex_object_3`, `ex_object_4`, `ex_object_5`
, `ex_object_6` , `ex_object_7` , `ex_object_8` , `ex_object_9` , `ex_object_10`
, `ex_object_11`, `ex_object_12`, `ex_object_13`, `ex_object_14`, `ex_object_15`
, `ex_object_16`, `ex_object_17`, `ex_object_18`, `ex_object_19`, `ex_object_20`
, `ex_object_21`, `ex_object_22`, `ex_object_23`, `ex_object_24`, `ex_object_25`
, `ex_object_26`, `ex_object_27`, `ex_object_28`, `ex_object_29`, `ex_object_30`
, `ex_object_31`, `ex_object_32`, `ex_object_33`, `ex_object_34`, `ex_object_35`
, `ex_object_36`, `ex_object_37`, `ex_object_38`, `ex_object_39`, `ex_object_40`
, `ex_object_41`, `ex_object_42`, `ex_object_43`, `ex_object_44`, `ex_object_45`
, `ex_object_46`, `ex_object_47`, `ex_object_48`, `ex_object_49`, `ex_object_50`
, `ex_object_51`, `ex_object_52`, `ex_object_53`, `ex_object_54`, `ex_object_55`
, `ex_object_56`, `ex_object_57`, `ex_object_58`, `ex_object_59`, `ex_object_60`
, `ex_object_61`, `ex_object_62`, `ex_object_63`, `ex_object_64`, `ex_object_65`
, `ex_object_66`, `ex_object_67`, `ex_object_68`, `ex_object_69`, `ex_object_70`
, `ex_object_71`, `ex_object_72`, `ex_object_73`, `ex_object_74`, `ex_object_75`;


*/

$prop_map = array(
  "binaire" => "bool",
  "binaire ssq" => "bool",
  "binaire / ssq" => "bool",

  "liste fermée" => "enum vertical|1 typeEnum|radio",
  "liste fermée ssq" => "enum vertical|1 typeEnum|radio",
  "liste fermée / ssq" => "enum vertical|1 typeEnum|radio",

  "texte court" => "str",
  "texte long" => "text",
  "date/heure" => "dateTime",
  "timestamp" => "dateTime",

  "numérique" => "float",
);

function reduce_whitespace($str) {
  return preg_replace("/\s+/", " ", $str);
}

if (!$file) {
  CAppUI::setMsg("Aucun fichier fourni", UI_MSG_WARNING);
}
else {
  CMbObject::$useObjectCache = false;
  $fp = fopen($file['tmp_name'], 'r');

  $keys = array(
    "concept_name_old", "concept_name", "concept_type",
    "list_name_old", "list_name",
    "tag_old", "tag_name_1", "tag_name_2",
    "field_name", "void",
  );
  $multiline = array();
  $line = array_fill_keys($keys, "");

  $current_class = null;
  $current_group = null;

  $line_number = 0;
  while ($current_line = fgetcsv($fp, null, $separator, $enclosure)) {
    $line_number++;

    $current_line = array_map("trim", $current_line);
    $current_line = array_map("reduce_whitespace", $current_line);
    $current_line = array_combine($keys, $current_line);

    foreach ($current_line as $_key => $_value) {
      if (in_array($_key, $multiline) && $_value == "") {
        $current_line[$_key] = $line[$_key];
      }
    }

    $line = $current_line;

    // EX CLASS
    if (empty($line["field_name"]) && empty($line["concept_name"])) {
      $current_class = new CExClass;
      $class = reset($line);

      $ds = $current_class->_spec->ds;
      $where = array(
        "name" => $ds->prepare("=%", $class),
      );
      $current_class->loadObject($where);

      if (!$current_class->_id) {
        $current_class->name = $class;
        $current_class->host_class = "CMbObject";
        $current_class->event = "void";
        $current_class->disabled = 1;
        //$current_class->required = 0;
        $current_class->conditional = ((stripos($class, "SSQ") !== false) ? 1 : 0);

        if ($msg = $current_class->store()) {
          CAppUI::setMsg("Ligne $line_number : $msg", UI_MSG_WARNING);
          continue;
        }
        else {
          CAppUI::setMsg("$current_class->_class-msg-create", UI_MSG_OK);
        }
      }

      $current_class->loadRefsGroups();
      $current_group = reset($current_class->_ref_groups);
      continue;
    }

    if (!$current_group || !$current_group->_id) {
      CAppUI::setMsg("Ligne $line_number sautée", UI_MSG_OK);
      continue;
    }

    // CONCEPT
    $concept = new CExConcept;
    $ds = $concept->_spec->ds;
    $where = array(
      "name" => $ds->prepare("=%", $line["concept_name"]),
    );

    $concept->loadObject($where);

    if (!$concept->_id) {
      CAppUI::setMsg("Ligne $line_number : concept non trouvé : <strong>{$line['concept_name']}</strong>", UI_MSG_WARNING);
      continue;
    }

    // FIELD
    $field = new CExClassField;
    CExClassField::$_load_lite = true;
    $field_name = empty($line["field_name"]) ? $line["concept_name"] : $line["field_name"];

    $ds = $field->_spec->ds;
    $where = array(
      "ex_class_field_translation.std" => $ds->prepare("=%", $field_name),
      "ex_class_field.ex_group_id" => $ds->prepare("=%", $current_group->_id),
    );

    $ljoin = array(
      "ex_class_field_translation" => "ex_class_field_translation.ex_class_field_id = ex_class_field.ex_class_field_id",
    );

    $field->loadObject($where, null, null, $ljoin);

    if (!$field->_id) {
      $field->name = uniqid("f");
      $field->_locale = $field_name;
      $field->ex_group_id = $current_group->_id;
      $field->concept_id = $concept->_id;

      if ($msg = $field->store()) {
        CAppUI::setMsg("Ligne $line_number : $msg", UI_MSG_WARNING);
        continue;
      }
      else {
        CAppUI::setMsg("$field->_class-msg-create", UI_MSG_OK);
      }
    }
  }

  fclose($fp);

  CAppUI::setMsg("Import terminé", UI_MSG_OK);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("message", CAppUI::getMsg());
$smarty->display("inc_import.tpl");
