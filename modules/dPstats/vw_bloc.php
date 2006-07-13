<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPbloc"      , "salle"   ));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

if(!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$debutact = mbGetValueFromGetOrSession("debutact", mbDate("-1 YEAR"));
$rectif   = mbTranformTime("+0 DAY", $debutact, "%d")-1;
$debutact = mbDate("-$rectif DAYS", $debutact);
$finact   = mbGetValueFromGetOrSession("finact", mbDate());
$rectif   = mbTranformTime("+0 DAY", $finact, "%d")-1;
$finact   = mbDate("-$rectif DAYS", $finact);
$finact   = mbDate("+ 1 MONTH", $finact);
$finact   = mbDate("-1 DAY", $finact);
$prat_id  = mbGetValueFromGetOrSession("prat_id", 0);
$salle_id = mbGetValueFromGetOrSession("salle_id", 0);
$codeCCAM = strtoupper(mbGetValueFromGetOrSession("codeCCAM", ""));

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$listSalles = new CSalle;
$where["stats"] = "= 1";
$order = "nom";
$listSalles = $listSalles->loadList($where, $order);

// Création du template
require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("debutact"  , $debutact  );
$smarty->assign("finact"    , $finact    );
$smarty->assign("prat_id"   , $prat_id   );
$smarty->assign("salle_id"  , $salle_id  );
$smarty->assign("codeCCAM"  , $codeCCAM  );
$smarty->assign("listPrats" , $listPrats );
$smarty->assign("listSalles", $listSalles);

$smarty->display("vw_bloc.tpl");

?>