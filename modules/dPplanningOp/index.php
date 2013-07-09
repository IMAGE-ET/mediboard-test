<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$user = CMediusers::get();

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_planning"   , TAB_EDIT);
$module->registerTab("vw_edit_planning"  , TAB_EDIT);
$module->registerTab("vw_edit_sejour"    , TAB_READ);

// Possibilité de planifier des interventions hors plage
$hors_plage = new CIntervHorsPlage();
if ($hors_plage->canRead()) {
  $module->registerTab("vw_edit_urgence" , TAB_READ);
}

$module->registerTab("vw_protocoles"     , TAB_EDIT);
//$module->registerTab("vw_edit_protocole" , TAB_EDIT);
$module->registerTab("vw_edit_typeanesth", TAB_ADMIN);
$module->registerTab("vw_idx_colors"     , TAB_ADMIN);
$module->registerTab("vw_sectorisations" , TAB_ADMIN);


// Droit d'acces a l'onglet seulement si on est praticien ou admin
if (($user->isPraticien() || $user->isFromType(array("Administrator"))) && CAppUI::conf("dPsalleOp CActeCCAM tarif")) {
  $module->registerTab("vw_edit_compta", TAB_EDIT);
}
