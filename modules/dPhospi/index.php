<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_placements"              , TAB_READ);

$module->registerTab("edit_sorties"               , TAB_READ);
$module->registerTab("vw_recherche"               , TAB_READ);
$module->registerTab("vw_suivi_bloc"              , TAB_READ);
$module->registerTab("form_print_planning"        , TAB_READ);
if (CAppUI::conf("dPhospi pathologies") || CAppUI::$user->isAdmin()) {
  $module->registerTab("vw_idx_pathologies"         , TAB_READ);
}
$module->registerTab("vw_idx_infrastructure"      , TAB_ADMIN);
$module->registerTab("vw_stats"                   , CAppUI::conf("dPhospi stats_for_all") ? TAB_EDIT : TAB_ADMIN);

if (CAppUI::conf("dPhospi systeme_prestations") == "standard") {
  $module->registerTab("vw_prestations_standard", TAB_ADMIN);
}
else {
  $module->registerTab("vw_prestations", TAB_ADMIN);
}

$module->registerTab("vw_etiquettes", TAB_ADMIN);
if (CModule::getInstalled("printing")) {
  $module->registerTab("vw_printers"              , TAB_READ);
}
if (CAppUI::conf("dPhospi use_vue_topologique")) {
  $module->registerTab("vw_plan_etage"            , TAB_READ);
}
