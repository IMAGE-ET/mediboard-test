<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

global $AppUI;

// Utilisateur courant
$curr_user = new CMediusers();
$curr_user->load($AppUI->user_id);

// RHS concernés
$rhs = new CRHS();
$rhs->load(CValue::get("rhs_id"));
if (!$rhs->_id) {
	CAppUI::stepAjax("RHS inexistant", UI_MSG_ERROR);
}

// Liste des catégories d'activité
$type_activite = new CTypeActiviteCdARR();
$types_activite = $type_activite->loadList();
$totaux = array();

if($rhs->_id) {
  $totaux[$rhs->_id] = array();
  foreach($types_activite as $_type) {
    $totaux[$rhs->_id][$_type->code] = 0;
  }
  $rhs->loadRefSejour();
  $rhs->loadBackRefs("lines");
  $_line = new CLigneActivitesRHS();
  foreach($rhs->_back["lines"] as $_line) {
    $_line->loadRefActiviteCdARR();
    $_line->_ref_code_activite_cdarr->loadRefTypeActivite();
    $totaux[$rhs->_id][$_line->_ref_code_activite_cdarr->_ref_type_activite->code] += $_line->_qty_total;
    $_line->loadRefIntervenantCdARR();
    $_line->loadFwdRef("executant_id", true);
    $_line->_fwd["executant_id"]->loadRefsFwd();
    $_line->_fwd["executant_id"]->loadRefCodeIntervenantCdARR();
  }
}

// Ligne vide d'activité
$rhs_line = new CLigneActivitesRHS();
if($curr_user->code_intervenant_cdarr) {
  $rhs_line->_executant             = $curr_user->_view;
  $rhs_line->executant_id           = $curr_user->user_id;
  $rhs_line->code_intervenant_cdarr = $curr_user->code_intervenant_cdarr;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("types_activite", $types_activite);
$smarty->assign("rhs_line"      , $rhs_line);
$smarty->assign("totaux"        , $totaux);
$smarty->assign("rhs"           , $rhs);

$smarty->display("inc_edit_rhs.tpl");

?>