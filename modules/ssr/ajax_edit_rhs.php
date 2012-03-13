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
if ($rhs->_id) {
  $rhs->loadRefSejour();
  $dependances = $rhs->loadRefDependances();
  if (!$dependances->_id) {
    $dependances->store();
  }
  $rhs->loadDependancesChronology();
  $rhs->buildTotaux();
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

$smarty->assign("rhs_line"          , $rhs_line);
$smarty->assign("rhs"               , $rhs);

$smarty->display("inc_edit_rhs.tpl");

?>