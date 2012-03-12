<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$sejour_ids  = explode("-", CValue::get("sejour_ids"));
$date_monday = CValue::get("date_monday");
$all_rhs     = CValue::get("all_rhs");

$where["sejour_id"] = CSQLDataSource::prepareIn($sejour_ids);
$where["date_monday"] = $all_rhs  ? ">= '$date_monday'" : "= '$date_monday'";

$order = "sejour_id, date_monday";

$rhs = new CRHS;
$sejours_rhs = $rhs->loadList($where, $order);

// Liste des catégories d'activité
$type_activite = new CTypeActiviteCdARR();
$types_activite = $type_activite->loadList();

$totaux = array();
foreach($sejours_rhs as $_rhs) {
  $totaux[$_rhs->_id] = array();
  foreach ($types_activite as $_type) {
    $totaux[$_rhs->_id][$_type->code] = 0;
  }
  $_rhs->loadRefSejour();
  $_rhs->loadRefDependances();
  if(!$_rhs->_ref_dependances->_id) {
    $_rhs->_ref_dependances->store();
  }
  $_rhs->loadBackRefs("lines");
  foreach($_rhs->_back["lines"] as $_line) {
    $_line->loadRefActiviteCdARR();
    $_line->_ref_activite_cdarr->loadRefTypeActivite();
    $totaux[$_rhs->_id][$_line->_ref_activite_cdarr->_ref_type_activite->code] += $_line->_qty_total;
    $_line->loadRefIntervenantCdARR();
    $_line->loadFwdRef("executant_id", true);
    $_line->_fwd["executant_id"]->loadRefsFwd();
    $_line->_fwd["executant_id"]->loadRefIntervenantCdARR();
  }
}

// Ligne vide d'activité
$rhs_line = new CLigneActivitesRHS();
$user = new CAppUI::$user;
if ($user->code_intervenant_cdarr) {
  $rhs_line->_executant             = $user->_view;
  $rhs_line->executant_id           = $user->user_id;
  $rhs_line->code_intervenant_cdarr = $user->code_intervenant_cdarr;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejours_rhs"    , $sejours_rhs);
$smarty->assign("rhs_line"       , $rhs_line);
$smarty->assign("types_activite" , $types_activite);
$smarty->assign("totaux"         , $totaux);
$smarty->assign("read_only"      , true);

$smarty->display("print_sejour_rhs_no_charge.tpl");

?>