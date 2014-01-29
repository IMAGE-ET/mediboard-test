<?php 

/**
 * $Id$
 *  
 * @category dPplanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$file = CValue::read($_FILES, "import");

$smarty = new CSmartyDP();

if (!$file) {
  $smarty->display("inc_import_mode_traitement_sejour.tpl");
  CApp::rip();
}

$group_id = CGroups::loadCurrent()->_id;

$csv = new CCSVFile($file["tmp_name"], CCSVFile::PROFILE_EXCEL);
$csv->readLine();

while ($line = $csv->readLine()) {
  list(
    $code,
    $libelle,
    $type_sejour,
    $type_pec,
    $actif
    ) = $line;

  $charge_price = new CChargePriceIndicator;
  $charge_price->code     = $code;
  $charge_price->libelle  = $libelle;
  $charge_price->type     = $type_sejour;
  $charge_price->type_pec = $type_pec;
  $charge_price->group_id = $group_id;
  $charge_price->loadMatchingObject();
  $charge_price->actif    = $actif;

  if ($msg = $charge_price->store()) {
    CAppUI::displayAjaxMsg($msg, UI_MSG_WARNING);
    continue;
  }
  CAppUI::displayAjaxMsg("importation terminée", UI_MSG_OK);
}

$smarty->display("inc_import_mode_traitement_sejour.tpl");