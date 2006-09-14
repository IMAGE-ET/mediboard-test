<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

$deb = mbDate();
$fin = mbDate("+ 0 day");

// Edite t'on un tarif ?
$tarif_id = mbGetValueFromGetOrSession("tarif_id", null);
$tarif = new CTarif;
$tarif->load($tarif_id);

// L'utilisateur est-il praticien ?
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$user = $mediuser->createUser();

// Liste des tarifs du praticien
if ($mediuser->isPraticien()) {
  $where = array();
  $where["function_id"] = "= 0";
  $where["chir_id"] = "= '$user->user_id'";
  $listeTarifsChir = new CTarif();
  $listeTarifsChir = $listeTarifsChir->loadList($where);
}
else
  $listeTarifsChir = null;

// Liste des tarifs de la spécialité
$where = array();
$where["chir_id"] = "= 0";
$where["function_id"] = "= '$mediuser->function_id'";
$listeTarifsSpe = new CTarif();
$listeTarifsSpe = $listeTarifsSpe->loadList($where);

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
if($user->user_type == 'Administrator' || $user->user_type == 'Secrétaire') {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
}
else
  $listPrat[0] = $user;

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('mediuser', $mediuser);
$smarty->assign('listeTarifsChir', $listeTarifsChir);
$smarty->assign('listeTarifsSpe', $listeTarifsSpe);
$smarty->assign('tarif', $tarif);
$smarty->assign('listPrat', $listPrat);

$smarty->display('vw_compta.tpl');

