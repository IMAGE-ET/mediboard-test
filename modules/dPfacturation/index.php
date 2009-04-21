<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPfacturation
 *	@version $Revision$
 *  @author Alexis / Yohann	
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_facture"      , null, TAB_READ);
$module->registerTab("vw_idx_factureitem"  , null, TAB_READ);

?>