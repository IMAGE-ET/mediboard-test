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

$planning = new CPlanningWeek("2010-03-29", "2010-03-27", "2010-03-30");
$planning->title = "Planning du patient '$patient->_view'";
$planning->guid = $sejour->_guid;

$i = 1;
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-03-29 10:30", 2.0*60, "Evt ".$i++, "#109618"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-03-30 10:20", 2.0*60, "Evt ".$i++));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-03-30 10:00", 1.0*60, "Evt ".$i++, "#9F1313"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-03-30 09:30", 2.0*60, "Evt ".$i++, "#9F1313"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-01 12:15", 1.5*60, "Evt ".$i++, "#B08B59"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-01 13:15", 1.5*60, "Evt ".$i++, "#109618"));
$planning->addEvent(new CPlanningEvent("CTruc-".$i, "2010-04-01 14:15", 1.5*60, "Evt ".$i++));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");
