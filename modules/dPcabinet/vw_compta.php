<?php /* $Id: vw_compta.php,v 1.15 2005/08/29 21:16:05 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.15 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers', 'functions') );
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcabinet', 'tarif') );

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
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('mediuser', $mediuser);
$smarty->assign('listeTarifsChir', $listeTarifsChir);
$smarty->assign('listeTarifsSpe', $listeTarifsSpe);
$smarty->assign('tarif', $tarif);
$smarty->assign('listPrat', $listPrat);

$smarty->display('vw_compta.tpl');

