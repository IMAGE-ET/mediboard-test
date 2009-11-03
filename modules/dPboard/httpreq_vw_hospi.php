<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
// Récupération des paramètres
$chirSel   = CValue::getOrSession("chirSel");
$date      = CValue::getOrSession("date", mbDate());
$board     = CValue::get("board", 0);

$where = array();
$where["praticien_id"] = "= '$chirSel'";
$where["entree_prevue"] = "<= '$date 23:59:59'";
$where["sortie_prevue"] = ">= '$date 00:00:00'";

$order = "`sortie_prevue` ASC, `entree_prevue` DESC";

$sejour = new CSejour();
$listSejours = $sejour->loadList($where, $order);

$affectation = new CAffectation();
foreach($listSejours as &$_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadRefGHM();
  $where = array();
  $where["sejour_id"] = "= '$_sejour->_id'";
  $where["entree"] = "<= '$date 00:00:00'";
  $where["sortie"] = ">= '$date 23:59:59'";
  
  $order = "`entree` DESC";
  
  $_sejour->_curr_affectations = $affectation->loadList($where, $order);
  foreach($_sejour->_curr_affectations as &$_aff) {
    $_aff->loadRefLit();
    $_aff->_ref_lit->loadCompleteView();
  }
}

// récupération des modèles de compte-rendu disponibles
$where = array();
$order = "nom";
$where["object_class"] = "= 'COperation'";
$where["chir_id"] = $ds->prepare("= %", $chirSel);
$crList    = CCompteRendu::loadModeleByCat("Opération", $where, $order, true);
$hospiList = CCompteRendu::loadModeleByCat("Hospitalisation", $where, $order, true);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("board"      , $board);
$smarty->assign("date"       , $date);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("crList"     , $crList);
$smarty->assign("hospiList"  , $hospiList);
$smarty->assign("nodebug"    , true);

$smarty->display("inc_vw_hospi.tpl");

?>