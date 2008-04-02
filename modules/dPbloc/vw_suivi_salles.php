<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloc 
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $can, $g;
$can->needsEdit();

$date_suivi  = mbGetValueFromGetOrSession("date_suivi", mbDate());

// Chargement des Anesthésistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Chargement des Chirurgiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

// Chargement des salles
$salle = new CSalle;
$where = array("group_id"=>"= '$g'");
$order = "'nom'";
$listSalles = $salle->loadListWithPerms(PERM_READ, $where, $order);
foreach ($listSalles as &$salle) {
  $salle->loadRefsForDay($date_suivi);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"     , true);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listSalles"     , $listSalles);
$smarty->assign("date_suivi"     , $date_suivi);
$smarty->assign("operation_id"   , 0);

$smarty->display("vw_suivi_salles.tpl");
?>