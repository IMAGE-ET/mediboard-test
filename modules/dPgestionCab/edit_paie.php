<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $AppUI, $can, $m;

$can->needsRead();

$user = new CMediusers();
$user->load($AppUI->user_id);

$employecab_id = CValue::getOrSession("employecab_id", null);
$fiche_paie_id = CValue::getOrSession("fiche_paie_id", null);

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
if (!$fichePaie->fiche_paie_id) {
  $fichePaie->debut = mbDate();
  $fichePaie->fin = mbDate();
  $fichePaie->params_paie_id = $paramsPaie->_id;
}

$listeFiches = $paramsPaie->loadBackRefs("fiches");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("employe"      , $employe);
$smarty->assign("fichePaie"    , $fichePaie);
$smarty->assign("listFiches"   , $listeFiches);
$smarty->assign("listEmployes" , $listEmployes);

$smarty->display("edit_paie.tpl");
?>