<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
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

$date             = mbGetValueFromGetOrSession("date"             , mbDate());
$datefin          = mbGetValueFromGetOrSession("datefin"          , mbDate());
$libelle          = mbGetValueFromGetOrSession("libelle"          , "");
$rubrique_id      = mbGetValueFromGetOrSession("rubrique_id"      , 0);
$mode_paiement_id = mbGetValueFromGetOrSession("mode_paiement_id" , 0);

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

$sql = "SELECT rubrique_id, SUM(montant) AS value" .
    "\nFROM `gestioncab`" .
    "\nWHERE date BETWEEN '$date' AND '$datefin'" .
    "\nAND function_id = '$user->function_id'";
if($libelle)
  $sql .= "\nAND libelle LIKE '%$libelle%'";
if($rubrique_id)
  $sql .= "\nAND rubrique_id = '$rubrique_id'";
if($mode_paiement_id)
  $sql .= "\nAND mode_paiement_id = '$mode_paiement_id'";
$sql .= "\nGROUP BY rubrique_id";
$totaux = db_loadList($sql);

$sql = "SELECT SUM(montant) AS value, 0 as invar" .
    "\nFROM `gestioncab`" .
    "\nWHERE date BETWEEN '$date' AND '$datefin'" .
    "\nAND function_id = '$user->function_id'";
if($libelle)
  $sql .= "\nAND libelle LIKE '%$libelle%'";
if($rubrique_id)
  $sql .= "\nAND rubrique_id = '$rubrique_id'";
if($mode_paiement_id)
  $sql .= "\nAND mode_paiement_id = '$mode_paiement_id'";
$sql .= "\nGROUP BY invar";
$total = db_loadResult($sql);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('date'             , $date);
$smarty->assign('datefin'          , $datefin);
$smarty->assign('libelle'          , $libelle);
$smarty->assign('rubrique_id'      , $rubrique_id);
$smarty->assign('mode_paiement_id' , $mode_paiement_id);
$smarty->assign('listRubriques'    , $listRubriques);
$smarty->assign('listModesPaiement', $listModesPaiement);
$smarty->assign('listGestionCab'   , $listGestionCab);
$smarty->assign('totaux'           , $totaux);
$smarty->assign('total'            , $total);

$smarty->display('print_rapport.tpl');
?>