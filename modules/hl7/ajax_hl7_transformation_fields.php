<?php

$actor_guid   = CValue::get("actor_guid");
$segment_name = CValue::get("segment_name");

$trans = new CHL7v2Transformation("2.5", null, "ADTA24");
$tree = $trans->getFieldsTree($segment_name);

$smarty = new CSmartyDP();
$smarty->assign("tree", $tree);
$smarty->display("vw_hl7v2_transformation.tpl");