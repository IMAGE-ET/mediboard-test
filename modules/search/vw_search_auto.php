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

$prescription_id = CValue::get("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefObject();
$user = CMediusers::get();
$favoris = new CSearchThesaurusEntry();
$targets = new CSearchTargetEntry();
$actes_ccam = array();
$diags_cim = array();
$results = array();

// On récupère les favoris
if ($prescription->_ref_object instanceof CSejour) {
  /** @var  $prescription->_ref_object CSejour*/
  foreach ($prescription->_ref_object->loadRefsActesCCAM() as $_ccam) {
    $diags_cim[] = $_ccam->code_acte;
  }

  if ($prescription->_ref_object->DP || $prescription->_ref_object->DR) {
    $diags_cim[] = $prescription->_ref_object->DP;
    $diags_cim[] = $prescription->_ref_object->DR;
  }

  foreach ($prescription->_ref_object->loadDiagnosticsAssocies(false) as $_das) {
    $diags_cim[] = $_das;
  }

  if (isset($actes_ccam)  || isset($diags_cim)) {
    $where["object_class"] = " = 'CCodeCIM10' OR object_class = 'CCodeCCAM'";
    $where["object_id"] = " ". CSQLDataSource::prepareIn($diags_cim);
    $targets = $targets->loadList($where);;
    foreach ($targets as $_target) {
      /** @var  $_target CSearchTargetEntry*/
      $tab_favoris_id[] = $_target->search_thesaurus_entry_id;
    }
    $whereFavoris["search_thesaurus_entry_id"] = " ". CSQLDataSource::prepareIn(array_unique($tab_favoris_id));
    $whereFavoris["user_id"] = "= '$user->_id'";
    $favoris = $favoris->loadList($whereFavoris);
  }
}

// On effectue la recherche automatique
if (isset($favoris)) {
  try {
    $search = new CSearch();
    $results = $search->searchAuto($favoris, $prescription->_ref_object);
  }
  catch (Exception $e) {
    CAppUI::displayAjaxMsg("search-not-connected", UI_MSG_ERROR);
    mbLog($e->getMessage());
  }
}

$smarty = new CSmartyDP();
$smarty->assign("sejour", $prescription->_ref_object);
$smarty->assign("results", $results);
$smarty->display("vw_search_auto.tpl");