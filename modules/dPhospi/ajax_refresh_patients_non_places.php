<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();

CCanDo::checkRead();
global $g;

// Récupération des paramètres
$date  = CValue::getOrSession("date", mbDateTime());
$services_ids    = CValue::getOrSession("services_ids");

$date_min = mbDateTime($date);
$date_max = mbDateTime("+1 day", $date_min);
$listNotAff = array(
  "Non placés" => array(),
  "Couloir" => array()
);

// Chargement des sejours n'ayant pas d'affectation pour cette période
$sejour = new CSejour();
$where = array();
$where["entree"] = "<= '$date_max'";
$where["sortie"] = ">= '$date_min'";
$where["annule"] = " = '0' ";
$where["group_id"] = "= '$g'";

$listNotAff["Non placés"] = $sejour->loadList($where);

foreach ($listNotAff["Non placés"] as $key => $_sejour) {
  $_sejour->loadRefsAffectations();
  if (!empty($_sejour->_ref_affectations)) {
    unset($listNotAff["Non placés"][$key]);
  }
  else {
    $_sejour->loadRefPatient();
  }
  $_sejour->checkDaysRelative($date);
}

// Chargement des affectations dans les couloirs (sans lit_id)
$where = array();
$ljoin = array();
$where["lit_id"] = "IS NULL";
$where["service_id"] = CSQLDataSource::prepareIn($services_ids);
$where["entree"] = "<= '$date_max'";
$where["sortie"] = ">= '$date_min'";

$affectation = new CAffectation();
$listNotAff["Couloir"] = $affectation->loadList($where, "entree ASC", null, null, $ljoin);

foreach ($listNotAff["Couloir"] as $_aff) {
  $_aff->loadView();
  $_aff->loadRefSejour();
  $_aff->_ref_sejour->checkDaysRelative($date);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_patients_notaff"  , $listNotAff);

$smarty->display("inc_patients_non_places.tpl");
?>