<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$chir_id  = CValue::get("chir_id");
$date_min = CValue::get("_date_min");
$date_max = CValue::get("_date_max");
$labo_id  = CValue::get("_labo_id");
$group_by = CValue::get("group_by");
$septic   = CValue::get("septic");

CValue::setSession("chir_id",   $chir_id);
CValue::setSession("_date_min", $date_min);
CValue::setSession("_date_max", $date_max);
CValue::setSession("_labo_id",  $labo_id);
CValue::setSession("group_by",  $group_by);
CValue::setSession("septic",    $septic);

$dmi_line = new CPrescriptionLineDMI;
$ds = $dmi_line->_spec->ds;

$where = array(
  "prescription_line_dmi.septic" => $ds->prepare("=%", $septic),
);
$ljoin = array(
  "operations" => "operations.operation_id = prescription_line_dmi.operation_id",
  //"product" => "product.product_id = prescription_line_dmi.product_id",
  "product_reference" => "product_reference.product_id = prescription_line_dmi.product_id",
	"plagesop" => "plagesop.plageop_id = operations.plageop_id",
);
$fields = array(
  "prescription_line_dmi.praticien_id",
	"product_reference.societe_id",
	"SUM(prescription_line_dmi.quantity) AS sum"
);

if ($chir_id) {
  $where["prescription_line_dmi.praticien_id"] = $ds->prepare("=%", $chir_id);
}

if ($labo_id) {
  $where["product_reference.societe_id"] = $ds->prepare("=%", $labo_id);
}

if ($date_min) {
  $where[] = "IF(operations.date, operations.date, plagesop.date) >= '$date_min'";
}

if ($date_max) {
  $where[] = "IF(operations.date, operations.date, plagesop.date) <= '$date_max'";
}

$group_by_map = array(
	"praticien" => "prescription_line_dmi.praticien_id",
	"labo"      => "product_reference.societe_id",
);

$dmi_lines_count = $dmi_line->countMultipleList($where, "total DESC", null, $group_by_map[$group_by], $ljoin, $fields);

foreach($dmi_lines_count as &$_stat) {
	$mediuser = new CMediusers;
	$mediuser->load($_stat["praticien_id"]);
	$mediuser->loadRefFunction();
	$_stat["praticien"] = $mediuser;
	
  $labo = new CSociete;
  $labo->load($_stat["societe_id"]);
  $_stat["labo"] = $labo;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dmi_lines_count", $dmi_lines_count);
$smarty->assign("group_by", $group_by);
$smarty->assign("septic", $septic);
$smarty->display("inc_vw_stats.tpl");
