<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$operation_id = CValue::get("operation_id");

$operation = new COperation;
$operation->load($operation_id);
$operation->loadRefPlageOp(1);

$sejour = $operation->loadRefSejour(true);

$grossesse = $sejour->loadRefGrossesse();
$grossesse->loadRefsSejours();
$grossesse->loadRefsConsultations();
$grossesse->_semaine_grossesse = ceil(CMbDT::daysRelative($grossesse->_date_fecondation, CMbDT::date($operation->_datetime)) / 7);
$grossesse->_terme_vs_operation = CMbDT::daysRelative($grossesse->terme_prevu, CMbDT::date($operation->_datetime));

$patient = $operation->loadRefPatient();

$smarty = new CSmartyDP;

$smarty->assign("operation", $operation);

$smarty->display("inc_vw_grossesse.tpl");

?>