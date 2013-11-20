<?php 

/**
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$motif_path = "modules/dPurgences/resources/motif_sfmu.csv";

$motif_csv = new CCSVFile($motif_path);
$motif_csv->jumpLine(1);
$count = 0;
while ($line = $motif_csv->readLine()) {
  list($libelle, $code) = $line;
  if (!$code) {
    continue;
  }
  $motif_sfmu = new CMotifSFMU();
  $motif_sfmu->code = $code;
  $motif_sfmu->libelle = $libelle;

  if ($msg = $motif_sfmu->store()) {
    CAppUI::stepAjax($msg, UI_MSG_ERROR);
    $count--;
  }
  $count++;
}
CAppUI::stepAjax("$count motif ajouté", UI_MSG_OK);