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

$planning = new CPlanningWeek;
$planning->title = "Planning du patient '$patient->_view'";
$planning->guid = $sejour->_guid;

$planning->addEvent(new CPlanningEvent("CTruc-1", "2010-03-29 10:30", 2*60, "Evt 1"));
$planning->addEvent(new CPlanningEvent("CTruc-2", "2010-03-30 10:20", 1.25*60, "Evt 2"));
$planning->addEvent(new CPlanningEvent("CTruc-3", "2010-03-30 12:15", 1.0*60, "Evt 3"));
$planning->addEvent(new CPlanningEvent("CTruc-4", "2010-04-01 15:15", 1.5*60, "Evt 4"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");
