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

$module->registerTab("vw_factures_cabinet", TAB_READ);
$module->registerTab("vw_factures_etab"   , TAB_READ);
$module->registerTab("vw_compta"          , TAB_READ);
if (CAppUI::conf("dPfacturation Other use_view_chainage")) {
  $module->registerTab("vw_edit_tarifs", TAB_READ);
}
if (CAppUI::conf("dPfacturation CRelance use_relances")) {
  $module->registerTab("vw_relances"        , TAB_READ);
}
if (CAppUI::conf("dPfacturation CRetrocession use_retrocessions")) {
  $module->registerTab("vw_retrocession_regles" , TAB_ADMIN);
}