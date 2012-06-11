<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$sejours_ids = CValue::get("sejours_ids");

// Chargement des séjours
$sejour = new CSejour;

$where = array();
$where["sejour_id"] = "IN ($sejours_ids)";

$sejours = $sejour->loadList($where);

$result = "";

foreach ($sejours as $_sejour) {
  $_operation = $_sejour->loadRefLastOperation();
  
  $consult_anesth = $_operation->loadRefsConsultAnesth();
  
  if ($consult_anesth->_id) {
    $result .= CApp::fetch("dPcabinet", "print_fiche", array("consultation_id" => $consult_anesth->consultation_id));
  }
}

if ($result != "") {
  echo $result;
}
else {
  echo "<h1>" . CAppUI::tr("CConsultAnesth.none") . "</h1>";
}
?>