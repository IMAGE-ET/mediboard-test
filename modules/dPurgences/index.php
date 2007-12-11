<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_rpu", null , TAB_READ);
$module->registerTab("vw_aed_rpu", null , TAB_READ);

?>