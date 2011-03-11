<?php /* $Id:$ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$owner_type = CValue::get("owner_type");
$id         = CValue::get("id");

$where[$owner_type] = " = '$id'";
$where["object_id"]     = " IS NULL";

$prescription = new CPrescription;
$nb_protocoles = $prescription->countList($where);

$smarty = new CSmartyDP;

$smarty->assign("nb_protocoles", $nb_protocoles);
$smarty->display("inc_intervalle_export.tpl");

?>