<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("edit_compta"			, TAB_READ);
$module->registerTab("edit_paie"  			, TAB_READ);
$module->registerTab("edit_params"			, TAB_READ);
$module->registerTab("edit_mode_paiement"	, TAB_READ);
$module->registerTab("edit_rubrique"		, TAB_READ);
?>