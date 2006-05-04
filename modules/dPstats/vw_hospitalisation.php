<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPhospi', 'service') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$debutact = mbGetValueFromGetOrSession("debutact", mbDate("-1 YEAR"));
$rectif = mbTranformTime("+0 DAY", $debutact, "%d")-1;
$debutact = mbDate("-$rectif DAYS", $debutact);
$finact   = mbGetValueFromGetOrSession("finact", mbDate());
$rectif = mbTranformTime("+0 DAY", $finact, "%d")-1;
$finact = mbDate("-$rectif DAYS", $finact);
$finact = mbDate("+ 1 MONTH", $finact);
$finact = mbDate("-1 DAY", $finact);
$prat_id  = mbGetValueFromGetOrSession("prat_id", 0);
$service_id = mbGetValueFromGetOrSession("service_id", 0);
$type_adm = mbGetValueFromGetOrSession("type_adm", 0);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$listServices = new CService;
$listServices = $listServices->loadList();

$listHospis = array();
$listHospis[0]["code"] = "comp";
$listHospis[0]["view"] = "Hospi complètes";
$listHospis[1]["code"] = "ambu";
$listHospis[1]["view"] = "Ambulatoires";
$listHospis[2]["code"] = "exte";
$listHospis[2]["view"] = "Externes";

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('debutact'    , $debutact);
$smarty->assign('finact'      , $finact);
$smarty->assign('prat_id'     , $prat_id);
$smarty->assign('service_id'  , $service_id);
$smarty->assign('type_adm'    , $type_adm);
$smarty->assign('listPrats'   , $listPrats);
$smarty->assign('listServices', $listServices);
$smarty->assign('listHospis'  , $listHospis);

$smarty->display('vw_hospitalisation.tpl');

?>