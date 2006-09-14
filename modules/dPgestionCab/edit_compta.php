<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

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
$where["date"]     = "BETWEEN '$date' AND '$datefin'";
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
$smarty = new CSmartyDP(1);

$smarty->assign("gestioncab"       , $gestioncab);
$smarty->assign("date"             , $date);
$smarty->assign("datefin"          , $datefin);
$smarty->assign("libelle"          , $libelle);
$smarty->assign("rubrique_id"      , $rubrique_id);
$smarty->assign("mode_paiement_id" , $mode_paiement_id);
$smarty->assign("listRubriques"    , $listRubriques);
$smarty->assign("listModesPaiement", $listModesPaiement);
$smarty->assign("listGestionCab"   , $listGestionCab);

$smarty->display("edit_compta.tpl");
?>