<?php /* $Id: edit_compta.php,v 1.2 2006/04/21 16:56:38 mytto Exp $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 1.2 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPgestionCab', 'gestionCab') );
require_once( $AppUI->getModuleClass('mediusers') );

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$user = new CMediusers();
$user->load($AppUI->user_id);

$gestioncab_id    = mbGetValueFromGetOrSession("gestioncab_id");
$date             = mbGetValueFromGetOrSession("date"             , mbDate());
$datefin          = mbGetValueFromGetOrSession("datefin"          , mbDate());
$libelle          = mbGetValueFromGetOrSession("libelle"          , "");
$rubrique_id      = mbGetValueFromGetOrSession("rubrique_id"      , 0);
$mode_paiement_id = mbGetValueFromGetOrSession("mode_paiement_id" , 0);

$gestioncab = new CGestionCab;
$gestioncab->load($gestioncab_id);
if(!$gestioncab->gestioncab_id) {
  $gestioncab->date = mbDate();
  $gestioncab->function_id = $user->function_id;
}

$where             = array();
$where[]           = "function_id = 0 OR function_id = '$user->function_id'";

$listRubriques     = new CRubrique;
$listRubriques     = $listRubriques->loadList($where);

$listModesPaiement = new CModePaiement;
$listModesPaiement = $listModesPaiement->loadList($where);

$listGestionCab    = new CGestionCab();
$where["date"]     = ">= '$date'";
if($libelle)
  $where["libelle"] = "LIKE '%$libelle%'";
if($rubrique_id)
  $where["rubrique_id"] = "= '$rubrique_id'";
if($mode_paiement_id)
  $where["mode_paiement_id"] = "= '$mode_paiement_id'";
$order = "date ASC";
$listGestionCab    = $listGestionCab->loadList($where, $order);
foreach($listGestionCab as $key => $fiche) {
  $listGestionCab[$key]->loadRefsFwd();
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('gestioncab'       , $gestioncab);
$smarty->assign('date'             , $date);
$smarty->assign('datefin'          , $datefin);
$smarty->assign('libelle'          , $libelle);
$smarty->assign('rubrique_id'      , $rubrique_id);
$smarty->assign('mode_paiement_id' , $mode_paiement_id);
$smarty->assign('listRubriques'    , $listRubriques);
$smarty->assign('listModesPaiement', $listModesPaiement);
$smarty->assign('listGestionCab'   , $listGestionCab);

$smarty->display('edit_compta.tpl');
?>