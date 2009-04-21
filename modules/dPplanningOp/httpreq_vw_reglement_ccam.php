<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author Alexis Granger
 */

// Recuperation de l'id de l'acte CCAM
$acte_ccam_id = mbGetValueFromGetOrSession("acte_ccam_id");

// Chargement de l'acte CCAM
$acte = new CActeCCAM();
$acte->load($acte_ccam_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("acte_ccam", $acte);
$smarty->display("inc_vw_reglement_ccam.tpl");

?>