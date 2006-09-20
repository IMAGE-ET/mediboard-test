<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_stock"      , "Edition des stocks"              , TAB_READ);
$module->registerTab("vw_idx_materiel"   , "Edition des Fiches matériel"     , TAB_READ);
$module->registerTab("vw_idx_category"   , "Gérer les catégories de matériel", TAB_READ);
$module->registerTab("vw_idx_fournisseur", "Fournisseurs"                    , TAB_READ);
$module->registerTab("vw_idx_refmateriel", "Références fournisseurs"         , TAB_READ);

?>