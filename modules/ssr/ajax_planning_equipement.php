<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCando::checkRead();

$date = CValue::getOrSession("date", mbDate());

$equipement = new CEquipement;
$equipement->load(CValue::get("equipement_id", 33));

$planning = new CPlanningWeek($date);
$planning->title = "Planning de l'équipement '$equipement->_view'";
$planning->guid = $equipement->_guid;

$date_min = reset(array_keys($planning->days));
$date_max = end(array_keys($planning->days));

// Chargement des evenement SSR 
$evenement_ssr = new CEvenementSSR();
$where["debut"] = "BETWEEN '$date_min' AND '$date_max'";
$where["equipement_id"] = " = '$equipement->_id'";
$evenements = $evenement_ssr->loadList($where);

foreach($evenements as $_evenement){
  $planning->addEvent(new CPlanningEvent($_evenement->_guid, $_evenement->debut, $_evenement->duree, $_evenement->code));
}
$planning->addEvent(new CPlanningEvent(null, mbDateTime(), null, null, "#ccc"));


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");


?>