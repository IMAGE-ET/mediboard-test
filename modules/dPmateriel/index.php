<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision$
 *  @author Sébastien Fillonneau
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_stock"      , TAB_READ);
$module->registerTab("vw_idx_commandes"  , TAB_READ);
$module->registerTab("vw_idx_materiel"   , TAB_READ);
$module->registerTab("vw_idx_category"   , TAB_READ);
$module->registerTab("vw_idx_fournisseur", TAB_READ);
$module->registerTab("vw_idx_refmateriel", TAB_READ);

?>