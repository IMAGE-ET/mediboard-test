<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */

// Recuperation du cip du produit
$cip = mbGetValueFromGetOrSession("CIP");

// Chargement du produit
$mbProduit = new CProduit();
$mbProduit->load($cip);
$mbProduit->loadRefMonographie();
//mbTrace($mbProduit->_ref_monographie);
$smarty = new CSmartyDP();

$smarty->assign("mbProduit", $mbProduit);

$smarty->display("vw_produit.tpl");

?>