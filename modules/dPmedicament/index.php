<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_recherche", null, TAB_READ);
$module->registerTab("vw_idx_livret", null, TAB_READ);
$module->registerTab("vw_idx_fiche_ATC", null, TAB_READ);

?>