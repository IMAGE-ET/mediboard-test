<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision: $
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_groups", null, TAB_READ);

?>