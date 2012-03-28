<?php /* $Id: ajax_edit_affectations.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$affectation_id = CValue::get("affectation_id");
$lit_id         = CValue::get("lit_id");

$affectation = new CAffectation;
$affectation->load($affectation_id);
$lit = new CLit;
$lit->load($affectation->lit_id);

$affectations = array();
$sejour_parturiente = null;

if (CModule::getActive("maternite")) {
  $naissance = new CNaissance;
  $naissance->sejour_enfant_id = $affectation->sejour_id;
  $naissance->loadMatchingObject();
  
  if ($naissance->_id) {
    $sejour_parturiente = $naissance->loadRefOperation()->loadRefSejour();
    $sejour_parturiente->loadRefPatient();
    $affectations = $sejour_parturiente->loadRefsAffectations();
    foreach ($affectations as $_affectation) {
      $_affectation->loadView();
    }
  }
}

$service = null;
if (!$affectation->_id) {
  $affectation->lit_id = $lit_id;
  $lit = $lit->load($lit_id);
	$lit->loadRefChambre()->loadRefService();
	$service = $lit->_ref_chambre->_ref_service;
	$service->loadRefsChambres();
	foreach($service->_ref_chambres as $chambre){
	  $chambre->loadRefsLits();
	}
}

$smarty = new CSmartyDP;

$smarty->assign("affectation" , $affectation);
$smarty->assign("affectations", $affectations);
$smarty->assign("lit"         , $lit);
$smarty->assign("lit_id"      , $lit_id);
$smarty->assign("sejour_parturiente", $sejour_parturiente);
$smarty->assign("service"     , $service);

$smarty->display("inc_edit_affectation.tpl");
?>