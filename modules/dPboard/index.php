<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

//$module->registerTab("vw_mainboard", null, TAB_READ);
$module->registerTab("vw_week", null, TAB_READ);
$module->registerTab("vw_day", null, TAB_READ);
$module->registerTab("vw_idx_sejour", null, TAB_READ);
//$module->registerTab("vw_interventions", null, TAB_READ);
$module->registerTab("vw_stats", null, TAB_READ);

?>