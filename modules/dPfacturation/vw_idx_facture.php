<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$facture_id = CValue::getOrSession("facture_id");

// Chargement de la facture demandé
$facture = new CFacture();
$facture->load($facture_id);
if($facture->load($facture_id)) {
	$facture->loadRefs();
	$facture->_ref_sejour->loadRefPatient();
}

$order = "date DESC";

// Récupération de la liste des factures
$itemFacture = new CFacture;
$listFacture = $itemFacture->loadList(null,$order);
foreach($listFacture as &$curr_facture) {
  $curr_facture->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture", $facture);
$smarty->assign("listFacture", $listFacture);
$smarty->display("vw_idx_facture.tpl");
?>
