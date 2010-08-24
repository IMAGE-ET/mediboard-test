<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
global $AppUI;

CCanDo::checkRead();

$rhs_ids = CValue::get("rhs_ids");

// Utilisateur courant
$user = new CMediusers();
$user->load($AppUI->user_id);

// Liste des catégories d'activité
$type_activite = new CTypeActiviteCdARR();
$types_activite = $type_activite->loadList();

$totaux = $sejours_rhs = array();
foreach($rhs_ids as $_rhs_id) {
  $rhs = new CRHS();
  $rhs->load($_rhs_id);
  $sejours_rhs[] = $rhs;
  $totaux[$rhs->_id] = array();
  foreach($types_activite as $_type) {
    $totaux[$rhs->_id][$_type->code] = 0;
  }
  $rhs->loadRefSejour();
  $rhs->loadRefDependances();
  if(!$rhs->_ref_dependances->_id) {
    $rhs->_ref_dependances->store();
  }
  $rhs->loadBackRefs("lines");
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
if($user->code_intervenant_cdarr) {
  $rhs_line->_executant             = $user->_view;
  $rhs_line->executant_id           = $user->user_id;
  $rhs_line->code_intervenant_cdarr = $user->code_intervenant_cdarr;
}

$rhs = new CRHS();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("rhs"            , $rhs);
$smarty->assign("sejours_rhs"    , $sejours_rhs);
$smarty->assign("rhs_line"       , $rhs_line);
$smarty->assign("types_activite" , $types_activite);
$smarty->assign("totaux"         , $totaux);
$smarty->assign("read_only"      , true);

$smarty->display("print_sejour_rhs_no_charge.tpl");

?>