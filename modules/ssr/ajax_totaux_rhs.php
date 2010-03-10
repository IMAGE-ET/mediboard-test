<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

// Séjour concernés
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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("types_activite", $types_activite);
$smarty->assign("totaux"        , $totaux);
$smarty->assign("rhs"           , $rhs);

$smarty->display("inc_totaux_rhs.tpl");

?>