<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

// @todo: Put the following in $config_dist;
global $dPconfig;
$dPconfig["dPImeds"]["url"] = "http://10.100.0.67/listedossiers.aspx";

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_results", "Consulter les résultats", TAB_READ);

?>