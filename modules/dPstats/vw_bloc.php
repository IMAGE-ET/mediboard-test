<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m;

$can->needsEdit();

$filter       = new COperation();
$filterSejour = new CSejour();

global $debutact, $finact, $prat_id, $salle_id, $bloc_id;
global $discipline_id, $codes_ccam;

$type_view_bloc = CValue::getOrSession("type_view_bloc", "nbInterv");

$debutact      = $filter->_date_min = CValue::getOrSession("_date_min", mbDate("-1 YEAR"));
$rectif        = mbTransformTime("+0 DAY", $filter->_date_min, "%d")-1;
$debutact      = $filter->_date_min = mbDate("-$rectif DAYS", $filter->_date_min);

$finact        = $filter->_date_max = CValue::getOrSession("_date_max",  mbDate());
$rectif        = mbTransformTime("+0 DAY", $filter->_date_max, "%d")-1;
$finact        = $filter->_date_max = mbDate("-$rectif DAYS", $filter->_date_max);
$finact        = $filter->_date_max = mbDate("+ 1 MONTH", $filter->_date_max);
$finact        = $filter->_date_max = mbDate("-1 DAY", $filter->_date_max);

$prat_id       = $filter->_prat_id = CValue::getOrSession("prat_id", 0);
$salle_id      = $filter->salle_id = CValue::getOrSession("salle_id", 0);
$bloc_id       = CValue::getOrSession("bloc_id");
$discipline_id = $filter->_specialite = CValue::getOrSession("discipline_id", 0);
$codes_ccam    = $filter->codes_ccam = strtoupper(CValue::getOrSession("codes_ccam", ""));
$discipline_id = $filter->_specialite = CValue::getOrSession("discipline_id", 0);

$type_hospi    = $filterSejour->type = CValue::getOrSession("type_hospi", "");

// map Graph Interventions
CAppUI::requireModuleFile("dPstats", "graph_activite");
CAppUI::requireModuleFile("dPstats", "graph_pratdiscipline");
CAppUI::requireModuleFile("dPstats", "graph_patjoursalle");
CAppUI::requireModuleFile("dPstats", "graph_op_annulees");
CAppUI::requireModuleFile("dPstats", "graph_occupation_salle");

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$bloc = new CBlocOperatoire();
$listBlocs = CGroups::loadCurrent()->loadBlocs();
$listBlocsForSalles = $listBlocs;

$bloc->load($bloc_id);
if ($bloc->_id) {
  foreach ($listBlocsForSalles as $key => &$curr_bloc) {
    if ($curr_bloc->_id != $bloc->_id) {
      unset ($listBlocsForSalles[$key]);
    }
  }
}

$listDisciplines = new CDiscipline();
$listDisciplines = $listDisciplines->loadUsedDisciplines();

// Statistiques de horaires
// $salle = new CSalle();
// $ds = $salle->_spec->ds;
// Ce script ne fontionne pas pour une raison inconnue.
// Fonctionne en direct dans PMA
// $horaires = $ds->loadList(file_get_contents("modules/dPstats/sql/horaires_salles.sql"));

$graphs = array();
if($type_view_bloc == "nbInterv") {
  $graphs[] = graphActivite($debutact, $finact, $prat_id, $salle_id, $bloc_id, $discipline_id, $codes_ccam, $type_hospi);
  $graphs[] = graphOpAnnulees($debutact, $finact, $prat_id, $salle_id, $bloc_id, $codes_ccam, $type_hospi);
  if($discipline_id) {
    $graphs[] = graphPraticienDiscipline($debutact, $finact, $prat_id, $salle_id, $bloc_id, $discipline_id, $codes_ccam, $type_hospi);
  }
} else {
  $listOccupation = graphOccupationSalle($debutact, $finact, $prat_id, $salle_id, $bloc_id, $codes_ccam, $type_hospi);
  $graphs[] = $listOccupation["total"];
  $graphs[] = $listOccupation["moyenne"];
  $graphs[] = graphPatJourSalle($debutact, $finact, $prat_id, $salle_id, $bloc_id, $codes_ccam, $type_hospi);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("type_view_bloc"          , $type_view_bloc    );
$smarty->assign("filter"                  , $filter            );
$smarty->assign("filterSejour"            , $filterSejour      );
$smarty->assign("listPrats"               , $listPrats         );
$smarty->assign("listBlocs"               , $listBlocs         );
$smarty->assign("listBlocsForSalles"      , $listBlocsForSalles);
$smarty->assign("bloc"                    , $bloc              );
$smarty->assign("listDisciplines"         , $listDisciplines   );
$smarty->assign("graphs"                  , $graphs            );

$smarty->display("vw_bloc.tpl");

?>