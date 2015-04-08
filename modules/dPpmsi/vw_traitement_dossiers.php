<?php 

/**
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();
$date       = CValue::getOrSession("date", CMbDT::date());
$type       = CValue::get("type");
$service_id = CValue::get("service_id");
$prat_id    = CValue::get("prat_id");

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
$smarty->assign("order_way"     , CValue::get("order_way", "ASC"));
$smarty->assign("order_col"     , CValue::get("order_col", "patient_id"));
$smarty->assign("tri_recept"    , CValue::get("tri_recept"));
$smarty->assign("tri_complet"   , CValue::get("tri_complet"));
$smarty->assign("date"          , $date);
$smarty->assign("period"        , CValue::get("period"));
$smarty->assign("filterFunction", CValue::get("filterFunction"));

$smarty->display("traitement_dossiers/vw_traitement_dossiers.tpl");