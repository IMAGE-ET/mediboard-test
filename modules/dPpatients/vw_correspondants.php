<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$medecin_id     = CValue::get("medecin_id");
$medecin_nom    = CValue::get("medecin_nom");
$medecin_prenom = CValue::get("medecin_prenom");
$medecin_cp     = CValue::get("medecin_cp");
$medecin_type   = CValue::get("medecin_type");
$new            = CValue::get("new");
$order_way      = CValue::getOrSession("order_way", "DESC");
$order_col      = CValue::getOrSession("order_col", "ccmu");

$smarty = new CSmartyDP();

$smarty->assign("medecin_id"    , $medecin_id);
$smarty->assign("medecin_nom"   , $medecin_nom);
$smarty->assign("medecin_prenom", $medecin_prenom);
$smarty->assign("medecin_cp"    , $medecin_cp);
$smarty->assign("medecin_type"  , $medecin_type);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("order_col"     , $order_col);
$smarty->assign("new"           , $new);

$smarty->display("vw_correspondants.tpl");
