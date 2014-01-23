<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$filter       = new COperation();
$filterSejour = new CSejour();

// Récupération des informations du formulaire

$hors_plage    = CValue::get("hors_plage", 1);

$debutact      = $filter->_date_min = CValue::get("_date_min", CMbDT::date("-1 YEAR"));
$rectif        = CMbDT::transform("+0 DAY", $filter->_date_min, "%d")-1;
$debutact      = $filter->_date_min = CMbDT::date("-$rectif DAYS", $filter->_date_min);

$finact        = $filter->_date_max = CValue::get("_date_max",  CMbDT::date());
$rectif        = CMbDT::transform("+0 DAY", $filter->_date_max, "%d")-1;
$finact        = $filter->_date_max = CMbDT::date("-$rectif DAYS", $filter->_date_max);
$finact        = $filter->_date_max = CMbDT::date("+ 1 MONTH", $filter->_date_max);
$finact        = $filter->_date_max = CMbDT::date("-1 DAY", $filter->_date_max);

$prat_id       = $filter->_prat_id = CValue::get("prat_id", 0);
$salle_id      = $filter->salle_id = CValue::get("salle_id", 0);
$bloc_id       = CValue::get("bloc_id");
$codes_ccam    = $filter->codes_ccam = strtoupper(CValue::get("codes_ccam", ""));
$discipline_id = $filter->_specialite = CValue::get("discipline_id", 0);

$type          = $filterSejour->type = CValue::getOrSession("type", "");

$user = new CMediusers();
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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"                  , $filter            );
$smarty->assign("filterSejour"            , $filterSejour      );
$smarty->assign("listPrats"               , $listPrats         );
$smarty->assign("listBlocs"               , $listBlocs         );
$smarty->assign("listBlocsForSalles"      , $listBlocsForSalles);
$smarty->assign("bloc"                    , $bloc              );
$smarty->assign("listDisciplines"         , $listDisciplines   );
$smarty->assign("hors_plage"              , $hors_plage        );

$smarty->display("vw_bloc.tpl");
