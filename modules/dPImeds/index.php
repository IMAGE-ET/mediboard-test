<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_results"      , TAB_READ);
$module->registerTab("vw_id_imeds"     , TAB_READ);
$module->registerTab("vw_soap_services", TAB_READ);

?>