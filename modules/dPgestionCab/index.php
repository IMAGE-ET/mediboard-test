<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("edit_compta"			  , null, TAB_READ);
$module->registerTab("edit_paie"  			  , null, TAB_READ);
$module->registerTab("edit_params"			  , null, TAB_READ);
$module->registerTab("edit_mode_paiement"	, null, TAB_READ);
$module->registerTab("edit_rubrique"		  , null, TAB_READ);
?>