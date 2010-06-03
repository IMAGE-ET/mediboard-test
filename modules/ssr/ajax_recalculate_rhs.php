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
$totaux[$rhs->_id] = array();
foreach($types_activite as $_type) {
  $totaux[$rhs->_id][$_type->code] = 0;
}
$rhs->loadRefSejour();
$rhs->loadRefDependances();
if(!$rhs->_ref_dependances->_id) {
  $rhs->_ref_dependances->store();
}

// Suppression des lignes d'activités du RHS
$rhs->loadBackRefs("lines");
foreach($rhs->_back["lines"] as $_line) {
  $_line->delete();
}
$rhs->loadBackRefs("lines");

// Ajout des lignes d'activités 
$evenementSSR = new CEvenementSSR();
$evenementSSR->sejour_id = $rhs->_ref_sejour->_id;
$evenementSSR->realise = 1;
$evenements = $evenementSSR->loadMatchingList();
foreach ($evenements as $_evenement) {
  $evenementRhs = $_evenement->getRHS();
  if ($evenementRhs->_id != $rhs->_id) {
    continue;
  }
  
  $_evenement->loadRefTherapeute();
  $therapeute = $_evenement->_ref_therapeute;
  
  $therapeute->loadRefCodeIntervenantCdARR();
  $code_intervenant_cdarr = $therapeute->_ref_code_intervenant_cdarr->code;
  
  $_evenement->loadRefsActesCdARR();
  $actes_cdarr = $_evenement->_ref_actes_cdarr;
  
  foreach ($actes_cdarr as $_acte_cdarr) {
    $ligne_activite_rhs = new CLigneActivitesRHS();
    $where["rhs_id"]                 = "= '$rhs->_id'";
    $where["executant_id"]           = "= '$therapeute->_id'";
    $where["code_activite_cdarr"]    = "= '$_acte_cdarr->code'";
    $where["code_intervenant_cdarr"] = "= '$code_intervenant_cdarr'";
    $ligne_activite_rhs->loadObject($where);
    
    $ligne_activite_rhs->incrementOrDecrementDay($_evenement->debut, "inc");
    
    if (!$ligne_activite_rhs->_id) {
      $ligne_activite_rhs->rhs_id                 = $rhs->_id;
      $ligne_activite_rhs->executant_id           = $therapeute->_id;
      $ligne_activite_rhs->code_activite_cdarr    = $_acte_cdarr->code;
      $ligne_activite_rhs->code_intervenant_cdarr = $code_intervenant_cdarr;
    }
    
    $ligne_activite_rhs->store();
  }
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