<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Etablissement courant
$group = CGroups::loadCurrent();
$date = CValue::getOrSession("date", mbDate());

// Plateaux disponibles
$plateau = new CPlateauTechnique;
$plateau->group_id = $group->_id;
$plateaux = $plateau->loadMatchingList();
foreach ($plateaux as $_plateau) {
  $_plateau->loadRefsTechniciens();
  foreach ($_plateau->_ref_techniciens as $_technicien) {
    $_technicien->loadRefCongeDate($date);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("plateaux", $plateaux);
$smarty->assign("bilan", new CBilanSSR);
$smarty->display("vw_idx_repartition.tpl");


?>