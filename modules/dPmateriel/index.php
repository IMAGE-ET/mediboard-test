<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision$
 *  @author Sébastien Fillonneau
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_stock"      , null, TAB_READ);
$module->registerTab("vw_idx_commandes"  , null, TAB_READ);
$module->registerTab("vw_idx_materiel"   , null, TAB_READ);
$module->registerTab("vw_idx_category"   , null, TAB_READ);
$module->registerTab("vw_idx_fournisseur", null, TAB_READ);
$module->registerTab("vw_idx_refmateriel", null, TAB_READ);

?>