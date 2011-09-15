<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$date_min  = CValue::get("_date_min");
$date_max  = CValue::get("_date_max");
$chir_id   = CValue::get("chir_id");
$labo_id   = CValue::get("_labo_id");
$code_lpp  = CValue::get("code_lpp");
$septic    = CValue::get("septic");
$csv       = CValue::get("csv");
$csv_title = CValue::get("csv_title");
$separator = CValue::get("separator", ";");
$delimiter = CValue::get("delimiter", "\"");

$dmi_line = new CPrescriptionLineDMI;
$ds = $dmi_line->_spec->ds;

$where = array(
  "prescription_line_dmi.septic" => $ds->prepare("=%", $septic),
);
$ljoin = array(
  "operations"        => "operations.operation_id = prescription_line_dmi.operation_id",
  "product"           => "product.product_id = prescription_line_dmi.product_id",
  "product_reference" => "product_reference.product_id = prescription_line_dmi.product_id",
	"plagesop"          => "plagesop.plageop_id = operations.plageop_id",
  "dmi"               => "dmi.product_id = product.product_id",
);
$fields = array(
  "prescription_line_dmi.praticien_id",
  "product_reference.societe_id",
  "prescription_line_dmi.product_id",
  "dmi.code_lpp",
	"SUM(prescription_line_dmi.quantity) AS sum"
);

if ($date_min) {
  $where[] = "IF(operations.date, operations.date, plagesop.date) >= '$date_min'";
}

if ($date_max) {
  $where[] = "IF(operations.date, operations.date, plagesop.date) <= '$date_max'";
}

if ($chir_id) {
  $where["prescription_line_dmi.praticien_id"] = $ds->prepare("=%", $chir_id);
}
elseif ($labo_id) {
  $where["product_reference.societe_id"] = $ds->prepare("=%", $labo_id);
}
elseif ($code_lpp) {
  $where["dmi.code_lpp"] = $ds->prepareLike("$code_lpp%");
}

$group_by = "prescription_line_dmi.product_id";

$dmi_lines_count = $dmi_line->countMultipleList($where, "product.name", $group_by, $ljoin, $fields);

//mbTrace($dmi_lines_count);

foreach($dmi_lines_count as &$_stat) {
	$mediuser = new CMediusers;
	$mediuser->load($_stat["praticien_id"]);
	$mediuser->loadRefFunction();
	$_stat["praticien"] = $mediuser;
	
  $labo = new CSociete;
  $labo->load($_stat["societe_id"]);
  $_stat["labo"] = $labo;
	
  $product = new CProduct;
  $product->load($_stat["product_id"]);
  $_stat["product"] = $product;
}

if ($csv) {
	$out = fopen("php://output", "w");
	header("Content-Type: application/csv");
	header("Content-Disposition: attachment; filename=Statistiques".($csv_title ? " $csv_title" : "").".csv");
	
  $line = array(
    "Praticien",
    "Laboratoire",
    "Produit",
    "Code",
    "Code LPP",
    "Total",
  );
	fputcsv($out, $line, $separator, $delimiter);

	foreach($dmi_lines_count as $_stat) {
    $line = array(
      $_stat["praticien"]->_view,
      $_stat["labo"]->_view,
      $_stat["product"]->_view,
      $_stat["product"]->code,
      $_stat["code_lpp"],
      $_stat["sum"],
		);
    fputcsv($out, $line, $separator, $delimiter);
	}
}
else {
	$smarty = new CSmartyDP();
	$smarty->assign("dmi_lines_count", $dmi_lines_count);
	$smarty->assign("group_by", $group_by);
	$smarty->assign("septic", $septic);
	$smarty->assign("date_min", $date_min);
	$smarty->assign("date_max", $date_max);
	$smarty->display("inc_vw_stats_detail.tpl");
}
