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

CCanDo::checkRead();

$date_min = CValue::getOrSession("date_min");
$date_max = CValue::getOrSession("date_max");
$group_id = CValue::getOrSession("group_id");
$concept_search = CValue::get("concept_search"); // concept values

CExClassField::$_load_lite = true;
CExObject::$_multiple_load = true;
CExObject::$_load_lite     = true;

$ex_class = new CExClass;

$where = array(
  "ex_link.group_id" => " = '$group_id'",
);
$ljoin = array();

$search = null;
if ($concept_search) {
  $concept_search = stripslashes($concept_search);
  $search = CExConcept::parseSearch($concept_search);
}

$ex_link = new CExLink();

$where["user_log.date"] = "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
$where["user_log.type"] = "= 'create'";
$where["user_log.object_class"] = "LIKE 'CExObject%'";

$where["ex_link.level"] = "= 'object'";

$ljoin["user_log"] =
  "user_log.object_id = ex_link.ex_object_id AND user_log.object_class = CONCAT('CExObject_',ex_link.ex_class_id)";
$ljoin["ex_class"] = "ex_class.ex_class_id = ex_link.ex_class_id";

$fields = array(
  "ex_link.ex_class_id",
);

$counts = $ex_link->countMultipleList($where, null, "ex_link.ex_class_id", $ljoin, $fields);

if (!empty($search)) {
  $ds = $ex_class->_spec->ds;
  $where["ex_class_field.concept_id"] = $ds->prepareIn(array_keys($search));

  $ljoin["ex_class_field_group"] = "ex_class_field_group.ex_class_id = ex_class.ex_class_id";
  $ljoin["ex_class_field"]       = "ex_class_field.ex_group_id = ex_class_field_group.ex_class_field_group_id";
  unset($where["user_log.object_class"]);
}

$ex_objects_counts = array();
foreach ($counts as $_row) {
  $_ex_class_id = $_row["ex_class_id"];

  $_count = $_row["total"];

  $_ex_link = new CExLink();

  if (!empty($search)) {
    $_ex_class = new CExClass();
    $_ex_class->load($_ex_class_id);

    $ljoin["user_log"] = "user_log.object_id = ex_link.ex_object_id AND user_log.object_class = 'CExObject_$_ex_class_id'";

    $ljoin_orig = $ljoin;
    $where_orig = $where;

    $where["ex_class.ex_class_id"] = "= '$_ex_class_id'";
    $where = array_merge($where, $_ex_class->getWhereConceptSearch($search));

    $ljoin["ex_object_$_ex_class_id"] = "ex_object_$_ex_class_id.ex_object_id = ex_link.ex_object_id";

    $_count = $_ex_link->countList($where, "ex_link.ex_class_id", $ljoin);

    $where = $where_orig;
    $ljoin = $ljoin_orig;
  }

  if ($_count > 0) {
    $ex_objects_counts[$_ex_class_id] = $_count;
  }
}

$ex_class = new CExClass();
$where = array(
  "ex_class_id" => $ex_class->getDS()->prepareIn(array_keys($ex_objects_counts)),
);
$ex_classes = $ex_class->loadList($where);

// Création du template
$smarty = new CSmartyDP("modules/forms");
$smarty->assign("ex_objects_counts", $ex_objects_counts);
$smarty->assign("ex_classes", $ex_classes);
$smarty->display("inc_list_ex_object_counts.tpl");
