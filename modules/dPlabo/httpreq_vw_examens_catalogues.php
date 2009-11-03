<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$catalogue_labo_id = CValue::getOrSession("catalogue_labo_id");

// Chargement du catalogue demandé
$catalogue = new CCatalogueLabo;
$catalogue->load($catalogue_labo_id);
$catalogue->loadRefs();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("search", 0);
$smarty->assign("catalogue"     , $catalogue    );

$smarty->display("inc_vw_examens_catalogues.tpl");
?>
