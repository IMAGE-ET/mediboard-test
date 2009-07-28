<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6518 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

// Selection de la date
$date = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();

// Chargement des urgences prises en charge
$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
  
$where["entree_reelle"] = "LIKE '$date%'";
$where["type"] = "= 'urg'";
$where[] = "(rpu.radio_debut IS NOT NULL) OR (rpu.bio_depart IS NOT NULL)";

$listSejours = $sejour->loadList($where, null, null, null, $ljoin);

foreach($listSejours as &$_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadRefRPU();
  $_sejour->_ref_rpu->loadRefSejourMutation();
  $_sejour->loadNumDossier();
  
  // Chargement de l'IPP
  $_sejour->_ref_patient->loadIPP();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listSejours", $listSejours);

$smarty->assign("date", $date);
$smarty->assign("today", $today);

$smarty->display("vw_radio.tpl");
?>