<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Filtres d'affichage

$date         = CValue::getOrSession("date", mbDate());
$type         = CValue::getOrSession("type");
$type_externe = CValue::getOrSession("type_externe");
$service_id   = CValue::getOrSession("service_id");

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00","+ 1 day");
$hier          = mbDate("- 1 day", $date);
$demain        = mbDate("+ 1 day", $date);

// Récupération de la liste des services
$where = array();
$where["externe"]   = "= '1'";
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

$sejour = new CSejour();
$sejour->_type_admission = $type;
$sejour->service_id      = $service_id;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"       , $sejour);
$smarty->assign("date_demain"  , $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"         , $date);
$smarty->assign("services"     , $services);
$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->display("vw_idx_permissions.tpl");

?>