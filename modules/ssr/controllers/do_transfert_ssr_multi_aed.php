<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


$sejour_id = CValue::post("sejour_id");
$conge_id = CValue::post("conge_id");
$replacer_id = CValue::post("replacer_id");

// Standard plage
$conge = new CPlageConge();
$conge->load($conge_id);

// Ugly hack du m_post
global $m;
$m = $m_post;

// Week dates
$date = CValue::getOrSession("date", CMbDT::date());
$monday = CMbDT::date("last monday", CMbDT::date("+1 DAY", $date));
$sunday = CMbDT::date("next sunday", CMbDT::date("-1 DAY", $date));

// Pseudo plage for user activity
if (preg_match("/[deb|fin][\W][\d]+/", $conge_id)) {
  list($activite, $user_id) = explode("-", $conge_id);
  $limit = $activite == "deb" ? $monday : $sunday;
  $conge = CPlageConge::makePseudoPlage($user_id, $activite, $limit);
}

// Events to be transfered
$evenement = new CEvenementSSR();
$where = array();
$date_min = max($monday, $conge->date_debut);
$date_max = CMbDT::date("+1 DAY", min($sunday, $conge->date_fin));
$where["therapeute_id"] = " = '$conge->user_id'";
$where["sejour_id"] = " = '$sejour_id'";
$where["debut"] = " BETWEEN '$date_min' AND '$date_max'";
$evenements = $evenement->loadList($where);
foreach ($evenements as $_evenement){
	$_evenement->therapeute_id = $replacer_id;
  $msg = $_evenement->store();
	CAppUI::displayMsg($msg, "CEvenementSSR-msg-modify");
} 

echo CAppUI::getMsg();
CApp::rip();

?>