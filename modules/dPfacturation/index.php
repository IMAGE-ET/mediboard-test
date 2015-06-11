<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

if (CAppUI::conf("dPfacturation CFactureCabinet view_bill")) {
  $module->registerTab("vw_factures_cabinet", TAB_READ);
}
if (CAppUI::conf("dPfacturation CFactureEtablissement view_bill")) {
  $module->registerTab("vw_factures_etab"   , TAB_READ);
}
$module->registerTab("vw_compta"            , TAB_READ);
if (CAppUI::conf("dPfacturation Other use_view_chainage")) {
  $module->registerTab("vw_edit_tarifs"     , TAB_READ);
}

if (CAppUI::conf("dPfacturation CRetrocession use_retrocessions")) {
  $module->registerTab("vw_retrocessions"   , TAB_READ);
  $module->registerTab("vw_retrocession_regles" , TAB_ADMIN);
}

if (CAppUI::conf("dPfacturation CReglement use_debiteur")) {
  $module->registerTab("vw_debiteurs"       , TAB_READ);
}

if (CAppUI::conf("ref_pays") == "2" && CAppUI::conf("dPfacturation Other see_reject_xml", CGroups::loadCurrent())) {
  $module->registerTab("vw_rejects_xml"       , TAB_READ);
}