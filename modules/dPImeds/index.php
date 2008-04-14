<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

// @todo: Put the following in $config_dist;
global $dPconfig;

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_results"      , null, TAB_READ);
$module->registerTab("vw_id_imeds"     , null, TAB_READ);
$module->registerTab("vw_soap_services", null, TAB_READ);

?>