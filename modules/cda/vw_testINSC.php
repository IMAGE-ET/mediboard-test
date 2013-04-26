<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$csv = new CCSVFile("modules/cda/resources/insc/Echantillon_de_test_INSC.csv", CCSVFile::PROFILE_EXCEL);
$csv->readLine();
$csv->readLine();
$patient = new CPatient();
$resultat = array("correct" => 0,
                  "incorrect" => 0);
while ($line = $csv->readLine()) {
  list(
    $firstName,
    $birthDate,
    $nir,
    $nirKey,
    $insc,
    $inscKey,
    ) = $line;

  $inscCal = $patient->calculINS_C($firstName, $birthDate, $nir.$nirKey);
  if ($inscCal["insc"] === $insc && $inscCal["cle_insc"] === $inscKey) {
    $resultat["correct"]++;
  }
  else {
    $resultat["incorrect"]++;
  }
}

$smarty = new CSmartyDP();
$smarty->assign("result", $resultat);
$smarty->display("vw_testINSC.tpl");