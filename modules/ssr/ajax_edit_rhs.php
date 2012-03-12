<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

// Utilisateur courant
$user = CMediusers::get();

// RHS concernés
$rhs = new CRHS();
$rhs->load(CValue::get("rhs_id"));
if (!$rhs->_id) {
  CAppUI::stepAjax("RHS inexistant", UI_MSG_ERROR);
}
$rhs->loadRefsNotes();

// Recalcul
if (CValue::get("recalculate")) {
  $rhs->recalculate();
}

// Liste des catégories d'activité
$type_activite = new CTypeActiviteCdARR();
$types_activite = $type_activite->loadList();
$totaux = array();
$executants = array();
$lines_by_executant = array();
if ($rhs->_id) {
  $totaux[$rhs->_id] = array();
  foreach($types_activite as $_type) {
    $totaux[$rhs->_id][$_type->code] = 0;
  }
  $rhs->loadRefSejour();
  $dependances = $rhs->loadRefDependances();
  if (!$dependances->_id) {
    $dependances->store();
  }
  $rhs->loadDependancesChronology();
	
  $_line = new CLigneActivitesRHS();
  foreach ($rhs->loadBackRefs("lines") as $_line) {
    $activite = $_line->loadRefActiviteCdARR();
    $type = $activite->loadRefTypeActivite();
    $totaux[$rhs->_id][$type->code] += $_line->_qty_total;
    $_line->loadRefIntervenantCdARR();
    $executant = $_line->loadFwdRef("executant_id", true);
    $executant->loadRefsFwd();
    $executant->loadRefIntervenantCdARR();
    $executants[$executant->_id] = $executant;
    $lines_by_executant[$executant->_id][] = $_line;
  }
}

// Ligne vide d'activité
$rhs_line = new CLigneActivitesRHS();
if ($user->code_intervenant_cdarr) {
  $rhs_line->_executant             = $user->_view;
  $rhs_line->executant_id           = $user->user_id;
  $rhs_line->code_intervenant_cdarr = $user->code_intervenant_cdarr;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("lines_by_executant", $lines_by_executant);
$smarty->assign("executants"        , $executants);
$smarty->assign("types_activite"    , $types_activite);
$smarty->assign("rhs_line"          , $rhs_line);
$smarty->assign("totaux"            , $totaux);
$smarty->assign("rhs"               , $rhs);

$smarty->display("inc_edit_rhs.tpl");

?>