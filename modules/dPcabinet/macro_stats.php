<?php /* $Id: print_select_docs.php 14587 2012-02-08 08:33:51Z alexis_granger $ */

 /**
  * @package Mediboard
  * @subpackage dPcabinet
  * @version $Revision: 14587 $
  * @author Sébastien Fillonneau
  */

CCanDo::checkAdmin();

$consult = new CConsultation;
$ds = $consult->_spec->ds;

$ref = mbDate("+14 days");

$dates = array();
foreach (range(0, 29) as $n) {
  $dates[] = $min = mbDate("- $n days", $ref);
}

$query = " SELECT COUNT(*) total, chir_id, date
  FROM `consultation`
  LEFT JOIN plageconsult AS plage ON plage.plageconsult_id = consultation.plageconsult_id
  WHERE date > '$min'
  GROUP BY chir_id, date
";

$totaux = array();
foreach ($result = $ds->loadList($query) as $_row) {
  $totaux[$_row["chir_id"]][$_row["date"]] = $_row["total"];
}

$user = CMediusers::get();
$users = $user->loadAll(array_keys($totaux));
foreach ($users as $_user) {
  $_user->loadRefFunction();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dates", $dates);
$smarty->assign("users", $users);
$smarty->assign("totaux", $totaux);

$smarty->display("macro_stats.tpl");
?>
