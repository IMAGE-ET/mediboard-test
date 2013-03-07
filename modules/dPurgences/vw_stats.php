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
$entree = CValue::getOrSession('entree', CMbDT::date());
$count  = CValue::getOrSession('count', 30);
$hide_cancelled = CValue::getOrSession("hide_cancelled", 1);

$filter = new CSejour;
$filter->entree = $entree;

$axes = array(
  "age"             => "Tranche d'âge",
  "sexe"            => CAppUI::tr("CPatient-sexe"),
  "ccmu"            => CAppUI::tr("CRPU-ccmu"),
  "mode_entree"     => CAppUI::tr("CSejour-mode_entree"),
  "mode_sortie"     => CAppUI::tr("CSejour-mode_sortie"),
  "provenance"      => CAppUI::tr("CSejour-provenance"),
  "destination"     => CAppUI::tr("CSejour-destination"),
  "orientation"     => CAppUI::tr("CRPU-orientation"),
  "transport"       => CAppUI::tr("CSejour-transport"),
  "without_rpu"     => "Séjours d'urgence sans RPU",
  "transfers_count" => "Nombre de transferts",
  "mutations_count" => "Nombre de mutations"
);

$axes_other = array(
  "radio" => "Attente radio",
  "bio"   => "Attente biologie",
  "spe"   => "Attente spécialiste",
  "duree_sejour"  => "Durée de séjour",
  "duree_pec"     => "Durée de prise en charge",
  "duree_attente" => "Durée d'attente",
  "diag_infirmier"  => "Diagnostic infirmier",
);

$smarty = new CSmartyDP();

$smarty->assign('filter', $filter);
$smarty->assign('axe', $axe);
$smarty->assign('axes', $axes);
$smarty->assign('axes_other', $axes_other);
$smarty->assign('count', $count);
$smarty->assign('hide_cancelled', $hide_cancelled);
$smarty->assign("ecap_installed", CModule::getActive("ecap"));

$smarty->display('vw_stats.tpl');
