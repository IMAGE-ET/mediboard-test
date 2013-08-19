<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$sejour_id    = CValue::get("sejour_id");
$libelle_ATC  = CValue::get("libelle_ATC");
$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

$transmission = new CTransmissionMedicale;
$transmissions = array();
$where = array();
$where["sejour_id"] = " = '$sejour_id'";

if ($object_id) {
  $where["object_id"] = " = '$object_id'";
  $where["object_class"] = " = '$object_class'";
}
else {
  $where["libelle_ATC"] = " LIKE '".addslashes($libelle_ATC)."'";
}

$order_by = "DATE DESC";

/** @var CTransmissionMedicale[] $transmissions */
$transmissions = $transmission->loadlist($where, $order_by);

foreach ($transmissions as $_transmission) {
  $_transmission->loadRefUser();
}

$smarty = new CSmartyDP;

$smarty->assign("transmissions", $transmissions);
$smarty->display("inc_list_transmissions_short.tpl");

