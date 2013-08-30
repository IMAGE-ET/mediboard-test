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
$csv->jumpLine(2);
$resultat = array("correct" => 0,
                  "incorrect" => 0,
                  "total" => 0);

while ($line = $csv->readLine()) {
  list(
    $firstName,
    $birthDate,
    $nir,
    $nirKey,
    $insc_csv,
    $insc_csv_Key,
    ) = $line;

  $firstName = CInscTools::formatString($firstName);
  $insc = CPatient::calculInsc($nir, $nirKey, $firstName, $birthDate);
  if ($insc === $insc_csv.$insc_csv_Key) {
    $resultat["correct"]++;
  }
  else {
    $resultat["incorrect"]++;
  }
  $resultat["total"]++;
}

$smarty = new CSmartyDP();
$smarty->assign("result", $resultat);
$smarty->display("inc_test_insc_auto.tpl");