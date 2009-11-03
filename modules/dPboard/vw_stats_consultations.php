<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $prat;

$filterConsultation = new CConsultation();

$filterConsultation->_date_min = CValue::getOrSession("_date_min", mbDate("-1 YEAR"));
$rectif = mbTransformTime("+0 DAY", $filterConsultation->_date_min, "%d") - 1;
$filterConsultation->_date_min = mbDate("-$rectif DAYS", $filterConsultation->_date_min);

$filterConsultation->_date_max =  CValue::getOrSession("_date_max",  mbDate());
$rectif = mbTransformTime("+0 DAY", $filterConsultation->_date_max, "%d") - 1;
$filterConsultation->_date_max = mbDate("-$rectif DAYS", $filterConsultation->_date_max);
$filterConsultation->_date_max = mbDate("+ 1 MONTH", $filterConsultation->_date_max);
$filterConsultation->_date_max = mbDate("-1 DAY", $filterConsultation->_date_max);


$filterConsultation->praticien_id = $prat->_id;

// Variables de templates
$smarty = new CSmartyDP();

$smarty->assign("filterConsultation", $filterConsultation);
$smarty->assign("prat"              , $prat);

$smarty->display("vw_stats_consultations.tpl");

?>