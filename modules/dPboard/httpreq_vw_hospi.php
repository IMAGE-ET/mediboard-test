<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPCabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Récupération des paramètres
$chirSel   = mbGetValueFromGetOrSession("chirSel");
$date      = mbGetValueFromGetOrSession("date", mbDate());
$board     = mbGetValueFromGet("board", 0);

$where = array();
$were["praticien_id"] = "= '$chirSel'";
$where["entree_prevue"] = "<= '$date 23:59:59'";
$where["sortie_prevue"] = ">= '$date 00:00:00'";

$order = "`sortie_prevue` ASC, `entree_prevue` DESC";

$sejour = new CSejour();
$listSejours = $sejour->loadList($where, $order);

$affectation = new CAffectation();
foreach($listSejours as $key => $curr_sejour) {
  $listSejours[$key]->loadRefsFwd();
  $where = array();
  $where["sejour_id"] = "= '$curr_sejour->_id'";
  $where["entree"] = "<= '$date 00:00:00'";
  $where["sortie"] = ">= '$date 23:59:59'";
  
  $order = "`entree` DESC";
  
  $listSejours[$key]->_curr_affectations = $affectation->loadList($where, $order);
  foreach($listSejours[$key]->_curr_affectations as $keyAff => $curr_aff) {
    $listSejours[$key]->_curr_affectations[$keyAff]->loadRefLit();
    $listSejours[$key]->_curr_affectations[$keyAff]->_ref_lit->loadCompleteView();
  }
}

// récupération des modèles de compte-rendu disponibles
$where = array();
$order = "nom";
$where["object_class"] = "= 'COperation'";
$where["chir_id"] = db_prepare("= %", $chirSel);
$crList    = CCompteRendu::loadModeleByCat("Opération", $where, $order, true);
$hospiList = CCompteRendu::loadModeleByCat("Hospitalisation", $where, $order, true);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("board"      , $board);
$smarty->assign("date"       , $date);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("crList"     , $crList);
$smarty->assign("hospiList"  , $hospiList);

$smarty->display("inc_vw_hospi.tpl");

?>