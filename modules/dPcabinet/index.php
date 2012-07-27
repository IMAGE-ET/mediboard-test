<?php 

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_planning"               , TAB_READ);
$module->registerTab("vw_journee"                , TAB_READ);
$module->registerTab("edit_planning"             , TAB_READ);
$module->registerTab("edit_consultation"         , TAB_EDIT);
//$module->registerTab("vw_dossier"                , TAB_EDIT);
$module->registerTab("form_print_plages"         , TAB_READ);
$module->registerTab("vw_compta"                 , TAB_EDIT);

if (CAppUI::conf("dPcabinet CConsultation consult_facture")) {
  $module->registerTab("vw_factures"             , TAB_EDIT);
}

$module->registerTab("vw_edit_tarifs"            , TAB_EDIT);
$module->registerTab("vw_categories"             , TAB_EDIT);
$module->registerTab("vw_banques"                , TAB_ADMIN);
$module->registerTab("vw_stats"                  , TAB_ADMIN);
$module->registerTab("offline_programme_consult" , TAB_ADMIN);
if (CModule::getActive("fse")) {
  $module->registerTab("vw_fse"                  , TAB_READ); 
}

if (CModule::getActive("dPprescription")) {
  $module->registerTab("vw_idx_livret", TAB_EDIT);
}

?>