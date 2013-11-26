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

$motif_sfmu = new CMotifSFMU();
$ds = $motif_sfmu->getDS();
$ds->exec("TRUNCATE TABLE motif_sfmu");
CAppUI::stepAjax("motifs supprim�s", UI_MSG_OK);

$handle = fopen($motif_path, "r");
$motif_csv = new CCSVFile($handle);
$motif_csv->jumpLine(1);
$count = 0;
$categorie = null;

while ($line = $motif_csv->readLine()) {
  list($libelle, $code) = $line;
  if (!$code) {
    $categorie = strtolower($libelle);
    continue;
  }
  $motif_sfmu = new CMotifSFMU();
  $motif_sfmu->code = $code;
  $motif_sfmu->libelle = $libelle;
  $motif_sfmu->categorie = $categorie;

  if ($msg = $motif_sfmu->store()) {
    CAppUI::stepAjax($msg, UI_MSG_ERROR);
    $count--;
  }
  $count++;
}

CAppUI::stepAjax("$count motif ajout�", UI_MSG_OK);