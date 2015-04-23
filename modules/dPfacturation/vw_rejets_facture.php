<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();
$chir_id      = CValue::getOrSession("chir_id");
$file_name    = CValue::getOrSession("file_name");
$num_facture  = CValue::getOrSession("num_facture");
$date         = CValue::getOrSession("date");
$motif_rejet  = CValue::getOrSession("motif_rejet");
$statut       = CValue::getOrSession("statut");
$name_assurance= CValue::getOrSession("name_assurance");

$where = array();
$where["praticien_id"] = " = '$chir_id'";
if ($num_facture) {   $where["num_facture"]  = " = '$num_facture'";}
if ($file_name) {     $where[".file_name"] = " LIKE '%$file_name%'";}
if ($date) {          $where["date"]  = " = '$date'";}
if ($motif_rejet) {   $where["motif_rejet"]  = " = '$motif_rejet'";}
if ($statut) {        $where["statut"]  = " = '$statut'";}
if ($name_assurance){ $where["name_assurance"]  = " = '$name_assurance'";}

$rejet = new CFactureRejet();
$order = "num_facture, date";
$rejets = $rejet->loadList($where, $order, null, "facture_rejet_id");

// Liste des chirurgiens
$user = new CMediusers();
$listChir =  $user->loadPraticiens(PERM_EDIT);

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("rejets"              , $rejets);

$smarty->display("vw_list_rejets.tpl");