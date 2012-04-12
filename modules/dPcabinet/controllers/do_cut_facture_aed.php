<?php /* $Id: do_cut_facture_aed.php $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$factureconsult_id = CValue::post("factureconsult_id");
for($i=0; $i<20; $i++){
	if(CValue::post("tarif$i")){
		$tarifs[$i] = CValue::post("tarif$i");
	}
}

$facture = new CFactureConsult();
$facture->load($factureconsult_id);

foreach($tarifs as $key=>$tarif){
	if($key!=0){
    $facture_sup = new CFactureConsult();
	  $facture_sup->type_facture  = $facture->type_facture;
	  $facture_sup->patient_id    = $facture->patient_id;
	  $facture_sup->ouverture     = $facture->ouverture;
	  $facture_sup->cloture       = $facture->cloture;
	  $facture_sup->du_patient    = $tarif;
	  
	  if ($msg = $facture_sup->store()) {
	    CAppUI::setMsg($msg, UI_MSG_ERROR);
	  }
	}
}

$facture->du_patient = $tarifs[0] + $facture->remise;
$facture->du_tiers   = 0;
if ($msg = $facture->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

echo CAppUI::getMsg();
CApp::rip();
?>