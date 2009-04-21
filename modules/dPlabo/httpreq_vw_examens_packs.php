<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$user = new CMediusers;
$user->load($AppUI->user_id);

$pack_examens_labo_id = mbGetValueFromGetOrSession("pack_examens_labo_id");

// Chargement du pack demandé
$pack = new CPackExamensLabo;
$pack->load($pack_examens_labo_id);
$pack->loadRefs();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pack"     , $pack     );

$smarty->display("inc_vw_examens_packs.tpl");

?>