<?php

$message = "ADTA28";

$trans = new CHL7v2Transformation("2.5", "FR_2.5", $message);
$tree = $trans->getTree();

$smarty = new CSmartyDP();
$smarty->assign("tree", $tree);
$smarty->display("vw_hl7v2_transformation.tpl");