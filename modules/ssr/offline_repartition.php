<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

CApp::setMemoryLimit("768M");


// Chargement de l'etablissement courant
$group = CGroups::loadCurrent();
$date = CMbDT::date();

// Chargement des plateaux disponibles
/** @var CSejour[][] $sejours */
$sejours = array();
/** @var CReplacement[][] $replacements */
$replacements = array();
/** @var CSejour[] $all_sejours */
$all_sejours = array();

$plateau = new CPlateauTechnique;
$plateau->group_id = $group->_id;

/** @var CPlateauTechnique[] $plateaux */
$plateaux = $plateau->loadMatchingList();
foreach ($plateaux as $_plateau) {
  foreach ($_plateau->loadRefsTechniciens() as $_technicien) {
    $_technicien->loadRefCongeDate($date);  
    
    $_technicien->loadRefKine();
    $kine_id = $_technicien->_ref_kine->_id;

    // Chargement des sejours du technicien
    $sejours[$_technicien->_id] = CBilanSSR::loadSejoursSSRfor($_technicien->_id, $date);

    /** @var CSejour $_sejour */
    foreach ($sejours[$_technicien->_id] as $_sejour) {
      $_sejour->checkDaysRelative($date);
      $_sejour->loadRefPatient(1);
      $_sejour->loadRefBilanSSR();
      $all_sejours[] = $_sejour;
    }
    
    // Chargement de ses remplacements
    $replacement = new CReplacement;
    $replacements[$_technicien->_id] = $replacement->loadListFor($kine_id, $date);

    /** @var CReplacement $_replacement */
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