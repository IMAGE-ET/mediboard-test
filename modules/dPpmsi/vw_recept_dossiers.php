<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$date       = CValue::getOrSession("date", CMbDT::date());
$type       = CValue::getOrSession("type");
$service_id = CValue::getOrSession("service_id");
$prat_id    = CValue::getOrSession("prat_id");

$sejour = new CSejour();
$sejour->_type_admission = $type;
$sejour->service_id      = explode(",", $service_id);
$sejour->praticien_id    = $prat_id;

// Récupération de la liste des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

// Récupération de la liste des praticiens
$prat = CMediusers::get();
$prats = $prat->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"        , $sejour);
$smarty->assign("services"      , $services);
$smarty->assign("prats"         , $prats);
$smarty->assign("order_way"     , CValue::getOrSession("order_way", "ASC"));
$smarty->assign("order_col"     , CValue::getOrSession("order_col", "patient_id"));
$smarty->assign("tri_recept"    , CValue::getOrSession("tri_recept"));
$smarty->assign("tri_complet"   , CValue::getOrSession("tri_complet"));
$smarty->assign("date"          , $date);
$smarty->assign("period"        , CValue::getOrSession("period"));
$smarty->assign("filterFunction", CValue::getOrSession("filterFunction"));

$smarty->display("reception_dossiers/vw_recept_dossiers.tpl");