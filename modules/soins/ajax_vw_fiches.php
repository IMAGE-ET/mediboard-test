<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get('sejour_id');

// Chargement des scores IGS
$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefsExamsIGS();
foreach ($sejour->_ref_exams_igs as $_igs) {
  $_igs->loadLastLog();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->display('inc_vw_fiches.tpl');

?>