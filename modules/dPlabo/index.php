<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_catalogues", null, TAB_READ);
$module->registerTab("vw_edit_examens"   , null, TAB_READ);

?>