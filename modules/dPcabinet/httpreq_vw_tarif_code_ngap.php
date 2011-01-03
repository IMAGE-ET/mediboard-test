<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

$acte = new CActeNGAP;
$acte->quantite    = CValue::get("quantite", "1");
$acte->code        = CValue::get("code");
$acte->coefficient = CValue::get("coefficient", "1");
$acte->demi        = CValue::get("demi");
$acte->complement  = CValue::get("complement");
$acte->updateMontantBase();
$acte->getLibelle();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("acte"  , $acte);
$smarty->display("inc_vw_tarif_ngap.tpl");


?>