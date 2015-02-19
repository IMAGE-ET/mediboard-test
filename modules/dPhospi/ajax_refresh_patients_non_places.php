<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $g;

CCanDo::checkEdit();
// Récupération des paramètres
$date         = CValue::getOrSession("date", CMbDT::dateTime());
$services_ids = CValue::getOrSession("services_ids");

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

$date_min = CMbDT::dateTime($date);
$date_max = CMbDT::dateTime("+1 day", $date_min);
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
  /* @var CSejour $_sejour*/
  $_sejour->loadRefsAffectations();
  if (!empty($_sejour->_ref_affectations)) {
    unset($listNotAff["Non placés"][$key]);
  }
  else {
    $_sejour->loadRefPatient()->loadRefDossierMedical(false);
  }
  $_sejour->checkDaysRelative($date);
  $_sejour->loadRefPrestation();
}
$dossiers = CMbArray::pluck($listNotAff["Non placés"], "_ref_patient", "_ref_dossier_medical");
CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");

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
  /* @var CAffectation $_aff*/
  $_aff->loadView();
  $sejour = $_aff->loadRefSejour();
  $sejour->loadRefPatient()->loadRefDossierMedical(false);
  $sejour->checkDaysRelative($date);
  $sejour->loadRefPrestation();
}

$dossiers = CMbArray::pluck($listNotAff["Couloir"], "_ref_sejour", "_ref_patient", "_ref_dossier_medical");
CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_patients_notaff"  , $listNotAff);

$smarty->display("inc_patients_non_places.tpl");
