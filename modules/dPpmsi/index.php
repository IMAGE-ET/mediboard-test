<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));
if (CAppUI::conf("dPpmsi display see_recept_dossier", CGroups::loadCurrent())) {
  $module->registerTab("vw_recept_dossiers" , TAB_READ);
}
$module->registerTab("vw_dossier_pmsi"    , TAB_EDIT);
$module->registerTab("vw_current_dossiers", TAB_READ);
$module->registerTab("vw_print_planning", TAB_READ);
if (CAppUI::conf("ref_pays") == "2") {
  $module->registerTab("vw_idx_sortie"    , TAB_READ);
}
if (CModule::getActive("atih")) {
  $module->registerTab("vw_traitement_dossiers"  , TAB_EDIT);
  $module->registerTab("vw_statistics_pmsi"    , TAB_ADMIN);
}
$module->registerTab('vw_cim10_explorer', TAB_READ);