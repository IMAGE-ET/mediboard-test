<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

CApp::setMemoryLimit("256M");


// Chargement de l'etablissement courant
$group = CGroups::loadCurrent();
$date = CMbDT::date();

// Chargement des plateaux disponibles
$all_sejours = array();
$plateau = new CPlateauTechnique;
$plateau->group_id = $group->_id;
$plateaux = $plateau->loadMatchingList();
foreach ($plateaux as $_plateau) {
  $_plateau->loadRefsTechniciens();
  
	foreach ($_plateau->_ref_techniciens as $_technicien) {
    $_technicien->loadRefCongeDate($date);	
		
		$_technicien->loadRefKine();
    $kine_id = $_technicien->_ref_kine->_id;

		// Chargement des sejours du technicien
		$sejours[$_technicien->_id] = CBilanSSR::loadSejoursSSRfor($_technicien->_id, $date);
    foreach($sejours[$_technicien->_id] as $_sejour){
		  $_sejour->checkDaysRelative($date);
		  $_sejour->loadRefPatient(1);
      $_sejour->loadRefBilanSSR();
      $all_sejours[] = $_sejour;
    }
		
		// Chargement de ses remplacements
    $replacement = new CReplacement;
		$replacements[$_technicien->_id] = $replacement->loadListFor($kine_id, $date);
		
		foreach ($replacements[$_technicien->_id] as $_replacement) {
		  // Détail sur le congé
		  $_replacement->loadRefConge();
		  $_replacement->_ref_conge->loadRefUser();
		  $_replacement->_ref_conge->_ref_user->loadRefFunction();
		  
		  // Détails des séjours remplacés
		  $_replacement->loadRefSejour();
		  $sejour =& $_replacement->_ref_sejour;
		  $sejour->checkDaysRelative($date);
		  $sejour->loadRefPatient(1);
      $sejour->loadRefBilanSSR();

      $all_sejours[] = $sejour;
		}
  }
}

// Couleurs
$colors = CColorLibelleSejour::loadAllFor(CMbArray::pluck($all_sejours, "libelle"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("plateaux", $plateaux);
$smarty->assign("sejours", $sejours);
$smarty->assign("colors", $colors);
$smarty->assign("replacements", $replacements);
$smarty->display("offline_repartition.tpl");

?>