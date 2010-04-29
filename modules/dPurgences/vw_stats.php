<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$axe    = CValue::getOrSession('axe');
$entree = CValue::getOrSession('entree', mbDate());

$filter = new CSejour;
$filter->entree = $entree;

$axes = array(
  "age" => "Tranche d'âge",
  "sexe" => CAppUI::tr("CPatient-sexe"),
  "ccmu" => CAppUI::tr("CRPU-ccmu"),
  "mode_entree" => CAppUI::tr("CRPU-mode_entree"),
  "mode_sortie" => CAppUI::tr("CSejour-mode_sortie"),
  "provenance" => CAppUI::tr("CRPU-provenance"),
  "destination" => CAppUI::tr("CRPU-destination"),
  "orientation" => CAppUI::tr("CRPU-orientation"),
  "transport" => CAppUI::tr("CRPU-transport"),
);

$smarty = new CSmartyDP();
$smarty->assign('filter', $filter);
$smarty->assign('axe', $axe);
$smarty->assign('axes', $axes);
$smarty->display('vw_stats.tpl');
