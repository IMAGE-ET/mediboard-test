<?php /* $Id ajax_cut_facture.php $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$factureconsult_id = CValue::getOrSession("factureconsult_id");
$nb_factures       = CValue::get("nb_factures", 2);

$facture = new CFactureConsult();
$facture->load($factureconsult_id);
$facture->loadRefsConsults();
$facture->loadRefReglements();

$proposition_tarifs = null;
if($nb_factures!=0){
	$proposition_tarifs = ($facture->du_patient + $facture->du_tiers - $facture->remise)/$nb_factures;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture"             , $facture);
$smarty->assign("proposition_tarifs"  , $proposition_tarifs);
$smarty->assign("nb_factures"         , $nb_factures);

$smarty->display("inc_cut_facture.tpl");
?>