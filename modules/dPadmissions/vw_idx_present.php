<?php /* $Id: vw_idx_admission.php 14407 2012-01-23 12:53:35Z flaviencrochard $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 14407 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Filtres d'affichage

$order_way  = CValue::getOrSession("order_way", "ASC");
$order_col  = CValue::getOrSession("order_col", "patient_id");
$date       = CValue::getOrSession("date", mbDate());
$type       = CValue::getOrSession("type");
$service_id = CValue::getOrSession("service_id");
$prat_id    = CValue::getOrSession("prat_id");

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00","+ 1 day");
$hier          = mbDate("- 1 day", $date);
$demain        = mbDate("+ 1 day", $date);

// Récupération de la liste des services
$where              = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$service            = new CService();
$services           = $service->loadGroupList($where);

// Récupération de la liste des praticiens
$prat = CMediusers::get();
$prats = $prat->loadPraticiens();

$sejour = new CSejour();
$sejour->_type_admission = $type;
$sejour->service_id      = explode(",", $service_id);
$sejour->praticien_id    = $prat_id;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"       , $sejour);
$smarty->assign("date_demain"  , $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"         , $date);
$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("services"     , $services);
$smarty->assign("prats"        , $prats);
$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->display("vw_idx_present.tpl");

?>