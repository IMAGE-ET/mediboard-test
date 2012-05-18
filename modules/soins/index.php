<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_sejour"        , TAB_READ);
if(CModule::getActive('dPprescription')){
  $module->registerTab("vw_pancarte_service"  , TAB_READ);
	$module->registerTab("vw_bilan_prescription", TAB_READ);
	$module->registerTab("vw_plan_soins_service", TAB_READ);
  if (CAppUI::conf("soins show_charge_soins")) {
	  $module->registerTab("vw_ressources_soins"  , TAB_READ);
  }
	//$module->registerTab("vw_dossier_sejour"    , TAB_READ);
}

if (isset(CModule::$active["dPstock"])) {
  $module->registerTab("vw_stocks_service"  , TAB_EDIT);
}

?>