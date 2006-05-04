<?php /* $Id: vw_reveil.php,v 1.5 2006/04/24 07:57:46 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPprotocoles
* @version $Revision: 1.5 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once($AppUI->getModuleClass("mediusers", "functions"));
require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPbloc", "salle"));
require_once($AppUI->getModuleClass("dPbloc", "plagesop"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

$date = mbGetValueFromGetOrSession("date", mbDate());

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();


// Selection des salles
$listSalles = new CSalle;
$listSalles = $listSalles->loadList();

// Selection des plages opératoires de la journée
$plages = new CplageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);
$listIdPlages = array();
foreach($plages as $key => $value) {
  $listIdPlages[] = "'".$value->id."'";
}

$timing = array();

$listReveil = new COperation;
$where = array();
if(count($listIdPlages))
  $where["plageop_id"] = "IN(".implode(",", $listIdPlages).")";
else
  $where[] = "1 = 0";
$where["entree_reveil"] = "IS NOT NULL";
$where["sortie_reveil"] = "IS NULL";
$order = "entree_reveil";
$listReveil = $listReveil->loadList($where, $order);
foreach($listReveil as $key => $value) {
  $listReveil[$key]->loadRefsFwd();
  $listReveil[$key]->loadRefsAffectations();
  if($listReveil[$key]->_ref_first_affectation->affectation_id) {
    $listReveil[$key]->_ref_first_affectation->loadRefsFwd();
    $listReveil[$key]->_ref_first_affectation->_ref_lit->loadRefsFwd();
    $listReveil[$key]->_ref_first_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  }
  $listReveil[$key]->_ref_plageop->loadRefsFwd();
  //Tableau des timmings
  $timing[$key]["entree_reveil"] = array();
  $timing[$key]["sortie_reveil"] = array();
  foreach($timing[$key] as $key2 => $value2) {
    for($i = -10; $i < 10 && $value->$key2 !== null; $i++) {
      $timing[$key][$key2][] = mbTime("+ $i minutes", $value->$key2);
    }
  }
}

$listOut = new COperation;
$where = array();
if(count($listIdPlages))
  $where["plageop_id"] = "IN(".implode(",", $listIdPlages).")";
else
  $where[] = "1 = 0";
$where["entree_reveil"] = "IS NOT NULL";
$where["sortie_reveil"] = "IS NOT NULL";
$order = "sortie_reveil DESC";
$listOut = $listOut->loadList($where, $order);
foreach($listOut as $key => $value) {
  $listOut[$key]->loadRefsFwd();
  $listOut[$key]->loadRefsAffectations();
  if($listOut[$key]->_ref_first_affectation->affectation_id) {
    $listOut[$key]->_ref_first_affectation->loadRefsFwd();
    $listOut[$key]->_ref_first_affectation->_ref_lit->loadRefsFwd();
    $listOut[$key]->_ref_first_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  }
  $listOut[$key]->_ref_plageop->loadRefsFwd();
  //Tableau des timmings
  $timing[$key]["entree_reveil"] = array();
  $timing[$key]["sortie_reveil"] = array();
  foreach($timing[$key] as $key2 => $value2) {
    for($i = -10; $i < 10 && $value->$key2 !== null; $i++) {
      $timing[$key][$key2][] = mbTime("+ $i minutes", $value->$key2);
    }
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('listSalles', $listSalles);
$smarty->assign('listAnesthType', dPgetSysVal("AnesthType"));
$smarty->assign('listAnesths', $listAnesths);
$smarty->assign('listChirs', $listChirs);
$smarty->assign('plages', $plages);
$smarty->assign('listReveil', $listReveil);
$smarty->assign('listOut', $listOut);
$smarty->assign('timing', $timing);
$smarty->assign('date', $date);

$smarty->display('vw_reveil.tpl');

?>