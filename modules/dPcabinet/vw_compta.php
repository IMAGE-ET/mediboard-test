<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$deb = mbDate();
$fin = mbDate("+ 0 day");

// Edite t'on un tarif ?
$tarif_id = mbGetValueFromGetOrSession("tarif_id", null);
$tarif = new CTarif;
$tarif->load($tarif_id);

// L'utilisateur est-il praticien ?
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefFunction();

// Liste des tarifs du praticien
$listeTarifsChir = null;
if ($mediuser->isPraticien()) {
  $where = array();
  $where["function_id"] = "IS NULL";
  $where["chir_id"] = "= '$mediuser->user_id'";
  $listeTarifsChir = new CTarif();
  $listeTarifsChir = $listeTarifsChir->loadList($where);
}

// Liste des tarifs de la spécialité
$where = array();
$where["chir_id"] = "IS NULL";
$where["function_id"] = "= '$mediuser->function_id'";
$listeTarifsSpe = new CTarif();
$listeTarifsSpe = $listeTarifsSpe->loadList($where);

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
$listPrat = in_array($mediuser->_user_type, array("Administrator", "Secrétaire")) ?
  $mediuser->loadPraticiens(PERM_READ) :
  array($mediuser->_id => $mediuser);
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('mediuser', $mediuser);
$smarty->assign('listeTarifsChir', $listeTarifsChir);
$smarty->assign('listeTarifsSpe', $listeTarifsSpe);
$smarty->assign('tarif', $tarif);
$smarty->assign('listPrat', $listPrat);

$smarty->display('vw_compta.tpl');

