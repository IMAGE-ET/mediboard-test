<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Utilisateur courant
$curr_user = CMediusers::get();

// Séjour concernés
$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
if (!$sejour->_id) {
	CAppUI::stepAjax("Séjour inexistant", UI_MSG_ERROR);
}

if ($sejour->type != "ssr") {
  CAppUI::stepAjax("Le séjour sélectionné n'est pas un séjour de type SSR (%s)", UI_MSG_ERROR, $sejour->type);
}

// Chargment du bilan
$sejour->loadRefBilanSSR();
$bilan = $sejour->_ref_bilan_ssr;

// Liste des catégories d'activité
$type_activite = new CTypeActiviteCdARR();
$types_activite = $type_activite->loadList();
$totaux = array();

// Liste des RHSs du séjour
$_rhs = new CRHS();
$rhss = CRHS::getAllRHSsFor($sejour);
foreach($rhss as $_rhs) {
  if($_rhs->_id) {
		
    $totaux[$_rhs->_id] = array();
    foreach($types_activite as $_type) {
      $totaux[$_rhs->_id][$_type->code] = 0;
    }
    
    $_rhs->loadRefsNotes();
    $_rhs->loadRefSejour();
    $_rhs->loadRefDependances();
    $_rhs->loadDependancesChronology();
		
    if(!$_rhs->_ref_dependances->_id) {
      $_rhs->_ref_dependances->store();
    }
		
    $_rhs->loadBackRefs("lines");
    foreach($_rhs->_back["lines"] as $_line) {
      $_line->loadRefIntervenantCdARR();
      $_line->loadFwdRef("executant_id", true);
      $_line->_fwd["executant_id"]->loadRefsFwd();
      $_line->_fwd["executant_id"]->loadRefIntervenantCdARR();

      if ($_line->code_activite_cdarr) {
        $activite = $_line->loadRefActiviteCdARR();
        $type = $activite->loadRefTypeActivite();
        $totaux[$_rhs->_id][$_line->_ref_activite_cdarr->_ref_type_activite->code] += $_line->_qty_total;
      }
    }
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

$smarty->assign("curr_user"     , $curr_user);
$smarty->assign("types_activite", $types_activite);
$smarty->assign("sejour"        , $sejour);
$smarty->assign("bilan"         , $bilan);
$smarty->assign("totaux"        , $totaux);
$smarty->assign("rhss"          , $rhss);
$smarty->assign("rhs_line"      , $rhs_line);

$smarty->display("inc_cotation_rhs.tpl");

?>