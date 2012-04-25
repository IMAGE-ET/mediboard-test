<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();

$rpu_id         = CValue::get("rpu_id");

// Chargement du RPU
$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejourMutation();

// Chargement du séjour
$sejour = $rpu->loadRefSejour();
$sejour->loadRefPatient()->loadIPP();
$sejour->loadNDA();
$sejour->loadRefsConsultations();

// Horaire par défaut
if (!$sejour->sortie_reelle) {
	$sejour->sortie_reelle = mbDateTime();
}

$where = array();
$where["entree"] = "<= '$sejour->sortie_reelle'";
$where["sortie"] = ">= '$sejour->sortie_reelle'";
$where["function_id"] = "IS NOT NULL";

$affectation = new CAffectation();
$blocages_lit = $affectation->loadList($where);

$where["function_id"] = "IS NULL";

foreach($blocages_lit as $blocage){
	$blocage->loadRefLit()->loadRefChambre()->loadRefService();
	$where["lit_id"] = "= '$blocage->lit_id'";
	
	if($affectation->loadObject($where))
	{
		$affectation->loadRefSejour();
		$affectation->_ref_sejour->loadRefPatient();
    $blocage->_ref_lit->_view .= " indisponible jusqu'à ".mbTransformTime($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y")." (".$affectation->_ref_sejour->_ref_patient->_view.")";
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rpu", $rpu);
$smarty->assign("sejour", $sejour);
$smarty->assign("blocages_lit", $blocages_lit);

$smarty->display("inc_edit_sortie.tpl");
?>