<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$token_field_evts = CValue::getOrSession("token_field_evts");

$_evenements = explode("|", $token_field_evts);
foreach($_evenements as $_evenement_id){
	$evenement = new CEvenementSSR();
	$evenement->load($_evenement_id);
	$evenement->loadRefSejour();
	$evenement->_ref_sejour->loadRefPatient();
	$evenement->loadRefElementPrescription();
	$evenements[$evenement->sejour_id][$evenement->_id] = $evenement;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("evenements", $evenements);
$smarty->display("inc_vw_modal_evenements.tpl");

?>