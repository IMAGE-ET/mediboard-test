<?php /* $Id: edit_compta.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 23 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$user = new CMediusers();
$user->load($AppUI->user_id);

$employecab_id = mbGetValueFromGetOrSession("employecab_id", null);
$fiche_paie_id = mbGetValueFromGetOrSession("fiche_paie_id", null);

$employe = new CEmployeCab;
$where = array();
$where["function_id"] = "= '$user->function_id'";

$listEmployes = $employe->loadList($where);
if(!count($listEmployes)) {
  $AppUI->setMsg("Vous devez avoir au moins un employé", UI_MSG_ERROR);
  $AppUI->redirect( "m=dPgestionCab&tab=edit_params" );
}
if($employecab_id) {
  $employe =& $listEmployes[$employecab_id];
} else {
  $employe = reset($listEmployes);
}

$paramsPaie = new CParamsPaie;
$paramsPaie->loadFromUser($employe->employecab_id);

$fichePaie = new CFichePaie();
$fichePaie->load($fiche_paie_id);
if(!$fichePaie->fiche_paie_id) {
  $fichePaie->debut = mbDate();
  $fichePaie->fin = mbDate();
}

$listeFiches = new CFichePaie();
$where = array();
if($paramsPaie->params_paie_id)
  $where["params_paie_id"] = "= $paramsPaie->params_paie_id";
else
  $where[] = "0 = 1";
$order = "debut DESC";
$listeFiches = $listeFiches->loadList($where, $order);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("employe"      , $employe);
$smarty->assign("fichePaie"    , $fichePaie);
$smarty->assign("paramsPaie"   , $paramsPaie);
$smarty->assign("listFiches"   , $listeFiches);
$smarty->assign("listEmployes" , $listEmployes);

$smarty->display("edit_paie.tpl");
?>