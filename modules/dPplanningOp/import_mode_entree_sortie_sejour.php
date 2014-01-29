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

$mode_class = CValue::get("mode_class");
$file       = CValue::read($_FILES, "import");

$smarty = new CSmartyDP();
$smarty->assign("mode_class", $mode_class);

if (!$file) {
  $smarty->display("inc_import_mode_entree_sortie_sejour.tpl");
  CApp::rip();
}

$group_id = CGroups::loadCurrent()->_id;

$csv = new CCSVFile($file["tmp_name"], CCSVFile::PROFILE_EXCEL);
$csv->readLine();

while ($line = $csv->readLine()) {
  list(
    $code,
    $libelle,
    $mode,
    $actif
    ) = $line;

  /** @var CModeEntreeSejour|CModeSortieSejour $mode_entree_sortie */
  $mode_entree_sortie = new $mode_class;
  $mode_entree_sortie->code     = $code;
  $mode_entree_sortie->libelle  = $libelle;
  $mode_entree_sortie->mode     = $mode;
  $mode_entree_sortie->group_id = $group_id;
  $mode_entree_sortie->loadMatchingObject();
  $mode_entree_sortie->actif    = $actif;


  if ($msg = $mode_entree_sortie->store()) {
    CAppUI::displayAjaxMsg($msg, UI_MSG_WARNING);
    continue;
  }
  CAppUI::displayAjaxMsg("importation terminée");
}

$smarty->display("inc_import_mode_entree_sortie_sejour.tpl");