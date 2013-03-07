<?php /* $Id: */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();


// @TODO : faire un detection de plage et non un chargement d'une journée
$date = CValue::get("date", CMbDT::date());



// Plages d'astreinte pour l'utilisateur
$plage_astreinte = new CPlageAstreinte();
$where = array();
$where[] = "((date_debut = '$date') OR (date_fin = '$date') OR (date_debut <='$date' AND date_fin >= '$date'))";
$plages_astreinte = $plage_astreinte->loadList($where);

foreach ($plages_astreinte as $_plage) {
  $_plage->_ref_user = $_plage->loadRefUser();
  $_plage->_type = $_plage->loadType();
}

$new_plageastreinte = new CPlageAstreinte();

$plage_id = CValue::get("plage_id");




// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plages_astreinte",   $plages_astreinte);
$smarty->assign("date",   $date);
$smarty->display("vw_list_day_astreinte.tpl");
?>

