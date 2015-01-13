<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
CCanDo::checkRead();

$prescription_id = CValue::get("prescription_id", null);
$sejour_id = CValue::get("sejour_id");
$_ref_object = new CSejour();
if ($sejour_id) {
  $sejour = new CSejour();
  $sejour->load($sejour_id);
  $_ref_object = $sejour;
  $contexte = "pmsi";
}

if ($prescription_id) {
  $prescription = new CPrescription();
  $prescription->load($prescription_id);
  $prescription->loadRefObject();
  $_ref_object = $prescription->_ref_object;
  $contexte = "pharmacie";
}

$user = CMediusers::get();
$favoris = new CSearchThesaurusEntry();
$targets = new CSearchTargetEntry();
$actes_ccam = array();
$diags_cim = array();
$results = array();
$tab_favoris = array();

$date  = CMbDT::date("-1 month");
$types = array();
$group = CGroups::loadCurrent();
if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
  $types = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
}

// On récupère les favoris
if ($_ref_object instanceof CSejour) {
  $date  = CMbDT::format($_ref_object->entree_reelle, "%Y-%m-%d");
  /** @var  $_ref_object CSejour*/
  // actes CCAM du séjour
  foreach ($_ref_object->loadRefsActesCCAM() as $_ccam) {
    $diags_actes[] = $_ccam->code_acte;
  }

  // actes CCAM du l'intervention
  foreach ($_ref_object->loadRefsOperations() as $_op) {
    foreach ($_op->loadRefsActesCCAM() as $_ccam) {
      $diags_actes[] = $_ccam->code_acte;
    }
  }

  if ($_ref_object->DP || $_ref_object->DR) {
    $diags_actes[] = $_ref_object->DP;
    $diags_actes[] = $_ref_object->DR;
  }

  foreach ($_ref_object->loadDiagnosticsAssocies(false) as $_das) {
    $diags_actes[] = $_das;
  }

  if (isset($diags_actes)) {
    $where["object_class"] = " = 'CCodeCIM10' OR object_class = 'CCodeCCAM'";
    $where["object_id"] = " ". CSQLDataSource::prepareIn($diags_actes);
    $targets = $targets->loadList($where);

    $tab_favoris_id = array();
    foreach ($targets as $_target) {
      /** @var  $_target CSearchTargetEntry*/
      $tab_favoris_id[] = $_target->search_thesaurus_entry_id;
    }
    $whereFavoris["search_thesaurus_entry_id"] = " ". CSQLDataSource::prepareIn(array_unique($tab_favoris_id));
    $whereFavoris["user_id"] = "= '$user->_id'";
    $whereFavoris["contextes"] = "= '$contexte'";
    $tab_favoris = $favoris->loadList($whereFavoris);
  }
}

// On effectue la recherche automatique
if (isset($tab_favoris)) {
  try {
    $search = new CSearch();
    $results = $search->searchAuto($tab_favoris, $_ref_object);
  }
  catch (Exception $e) {
    CAppUI::displayAjaxMsg("search-not-connected", UI_MSG_ERROR);
    mbLog($e->getMessage());
  }
}

$smarty = new CSmartyDP();
$smarty->assign("sejour", $_ref_object);
$smarty->assign("sejour_id", $_ref_object->_id);
$smarty->assign("results", $results);
$smarty->assign("date", $date);
$smarty->assign("types", $types);
$smarty->assign("contexte", $contexte);
$smarty->display("vw_search_auto.tpl");