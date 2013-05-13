<?php

/**
 * $Id$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$sejours_ids = CValue::get("sejours_ids");

// Chargement des séjours
$sejour = new CSejour();

$where = array();
$where["sejour_id"] = "IN ($sejours_ids)";

$sejours = $sejour->loadList($where);

$result = "";

$last_sejour = end($sejours);

CMbObject::massLoadFwdRef($sejours, "patient_id");

foreach ($sejours as $_sejour) {
  $_sejour->loadRefPatient();
}

// Tri par nom de patient
$sorter_nom    = CMbArray::pluck($sejours, "_ref_patient", "nom");
$sorter_prenom = CMbArray::pluck($sejours, "_ref_patient", "prenom");
array_multisort($sorter_nom, SORT_ASC, $sorter_prenom, SORT_ASC, $sejours);

foreach ($sejours as $_sejour) {
  $_operation = $_sejour->loadRefLastOperation();
  
  if (!$_operation->_id) {
    continue;
  }
  
  $consult_anesth = $_operation->loadRefsConsultAnesth();
  
  if ($consult_anesth->_id) {
    $result .= CApp::fetch(
      "dPcabinet", "print_fiche", array (
        "dossier_anesth_id" => $consult_anesth->_id,
        "offline"         => 1,
        "multi"           => 1,
      )
    );
    
    if ($_sejour != $last_sejour ) {
      $result .= "<br style=\"page-break-after: always;\" />";
    }
  }
}

echo $result != "" ?
  $result :
  "<h1>" . CAppUI::tr("CConsultAnesth.none") . "</h1>";