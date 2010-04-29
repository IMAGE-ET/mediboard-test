<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefPatient();

$patient = $sejour->_ref_patient;

//$planning = new CPlanningWeek("2010-04-07", "2010-04-05", "2010-04-10");
$planning = new CPlanningWeek(mbDate());
$planning->title = "Planning du patient '$patient->_view'";
$planning->guid = $sejour->_guid;

$date_min = reset(array_keys($planning->days));
$date_max = end(array_keys($planning->days));

// Chargement des evenement SSR 
$evenement_ssr = new CEvenementSSR();
$where["sejour_id"] = " = '$sejour->_id'";
$where["debut"] = "BETWEEN '$date_min' AND '$date_max'";
$evenements = $evenement_ssr->loadList($where);

foreach($evenements as $_evenement){
  $planning->addEvent(new CPlanningEvent($_evenement->_guid, $_evenement->debut, $_evenement->duree, $_evenement->code));
}
$planning->addEvent(new CPlanningEvent(null, mbDateTime(), null, null, "#ccc"));

/*
$i = 1;
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-05 10:30", 2.0*60, "Evt ".$i++, "#109618"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-06 10:20", 2.0*60, "Evt ".$i++));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-06 10:00", 1.0*60, "Evt ".$i++, "#9F1313"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-06 09:30", 2.0*60, "Evt ".$i++, "#9F1313"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-08 12:15", 1.5*60, "Evt ".$i++, "#B08B59"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-08 13:15", 1.5*60, "Evt ".$i++, "#109618"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-08 14:15", 1.5*60, "Evt ".$i++));
$planning->addEvent(new CPlanningEvent(null, mbDateTime()));
*/

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");
