<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

// Sejour SSR
$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefPatient();

$date = CValue::getOrSession("date", mbDate());

$monday = mbDate("last monday", mbDate("+1 day", $date));
$sunday = mbDate("next sunday", $date);

for ($i = 0; $i < 7; $i++) {
	$_date = mbDate("+$i day", $monday);
  $list_days[$_date] = mbTransformTime(null, $_date, "%a");
}

// Prescription
$sejour->loadRefPrescriptionSejour();
$prescription =& $sejour->_ref_prescription_sejour;
$prescription->loadRefsLinesElementByCat();

// Chargements des codes cdarrs des elements de prescription
$categories = array();
foreach ($prescription->_ref_prescription_lines_element_by_cat as $_lines_by_chap){
  foreach ($_lines_by_chap as $_lines_by_cat){
    foreach ($_lines_by_cat['element'] as $_line){
    	$cat = $_line->_ref_element_prescription->_ref_category_prescription;
    	if(!array_key_exists($cat->_id, $categories)){
    	  $categories[$cat->_id] = $cat;
      }
    	$_line->_ref_element_prescription->loadBackRefs("cdarrs");
    }
	}
}

// Bilan
$sejour->loadRefBilanSSR();
$bilan =& $sejour->_ref_bilan_ssr;
$bilan->loadRefTechnicien();
$bilan->_ref_technicien->loadRefKine();

// Technicien et plateau
$technicien = new CTechnicien;
$plateau = new CPlateauTechnique;
if ($technicien->_id = $bilan->technicien_id) {
	$technicien->loadMatchingObject();
	$plateau = $technicien->loadFwdRef("plateau_id");
	$plateau->loadRefsEquipements();
  $plateau->loadRefsTechniciens();
}

// Chargement de tous les plateaux et des equipements et techniciens associés
$plateau_tech = new CPlateauTechnique();
$plateau_tech->group_id = CGroups::loadCurrent()->_id;
$plateaux = $plateau_tech->loadMatchingList();
foreach($plateaux as $_plateau_tech){
	$_plateau_tech->loadRefsEquipements();
	$_plateau_tech->loadRefsTechniciens();
}

$evenement_ssr = new CEvenementSSR();

// Chargement des executants en fonction des category de prescription
foreach($categories as $_category){
	// Chargement des associations pour chaque catégorie
  $function_categorie = new CFunctionCategoryPrescription();
	$function_categorie->category_prescription_id = $_category->_id;
	$associations[$_category->_id] = $function_categorie->loadMatchingList();
  
	// Parcours des associations trouvées et chargement des utilisateurs
	foreach($associations[$_category->_id] as $_assoc){
		$_assoc->loadRefFunction();
		$function =& $_assoc->_ref_function;
		$function->loadRefsUsers();
		foreach($function->_ref_users as $_user){
			// On verifie sur le kine fait parti du plateau
			if($_user->isKine()){
				$technicien = new CTechnicien();
				$technicien->kine_id = $_user->_id;
				$technicien->plateau_id = $plateau->_id;
			  if($technicien->countMatchingList()){
			  	$executants[$_category->_id][] = $_user;
			  }
			} 
			// Si le user n'est pas un kine, on le rajoute dans la liste
			else {
				$executants[$_category->_id][] = $_user;
      }
		}
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("evenement_ssr", $evenement_ssr);
$smarty->assign("list_days", $list_days);
$smarty->assign("sejour" , $sejour);
$smarty->assign("bilan"  , $bilan);
$smarty->assign("plateau", $plateau);
$smarty->assign("prescription", $prescription);
$smarty->assign("plateaux", $plateaux);
$smarty->assign("executants", $executants);
$smarty->display("inc_activites_sejour.tpl");

?>