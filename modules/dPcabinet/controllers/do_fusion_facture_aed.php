<?php /* $Id: do_fusion_facture_aed.php $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$factureconsult_id = CValue::post("factureconsult_id");

$facture = new CFactureConsult();
$facture->load($factureconsult_id);
$facture->loadRefs();

$facture->du_patient = 0;
foreach ($facture->_ref_consults as $consult){
	$consult->loadRefsActes();
	foreach($consult->_ref_actes_tarmed as $acte_tarmed){
		$facture->du_patient += $acte_tarmed->montant_base; 
	}
}

$facture->_montant_sans_remise = null;
$facture->_montant_avec_remise = null;
if ($msg = $facture->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

$where = array();
$where["factureconsult_id"] = "!= '$facture->_id'";
$where["patient_id"] = "= '$facture->patient_id'";
$where["ouverture"]  = "= '$facture->ouverture'";
$where["cloture"]    = "= '$facture->cloture'";
$where["type_facture"] = "= '$facture->type_facture'";
$factures = $facture->loadList( $where, "factureconsult_id DESC");

foreach($factures as $_facture){
	if ($msg = $_facture->delete()) {
	  CAppUI::setMsg($msg, UI_MSG_ERROR);
	}
}

echo CAppUI::getMsg();
CApp::rip();
?>