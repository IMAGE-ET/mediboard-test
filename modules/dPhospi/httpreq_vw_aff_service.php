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

CCanDo::checkRead();

global $m;
CAppUI::requireModuleFile($m, "inc_vw_affectations");

$date       = CValue::getOrSession("date", CMbDT::date());
$mode       = CValue::getOrSession("mode", 0);
$service_id = CValue::get("service_id");

// Chargement du service
$service = new CService();
$service->load($service_id);
loadServiceComplet($service, $date, $mode);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"        , $date );
$smarty->assign("demain"      , CMbDT::date("+ 1 day", $date));
$smarty->assign("curr_service", $service);

$smarty->display("inc_affectations_services.tpl");

