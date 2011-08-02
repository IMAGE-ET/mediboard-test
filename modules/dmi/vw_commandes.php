<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date_min = CValue::getOrSession("_date_min", mbDate());
$date_max = CValue::getOrSession("_date_max", mbDate("+1 DAY"));
$type     = CValue::getOrSession("type");

$dmi_line = new CPrescriptionLineDMI();
$dmi_line->type = $type;

$interv = new COperation;
$interv->_date_min = $date_min;
$interv->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dmi_line", $dmi_line);
$smarty->assign("interv"  , $interv);
$smarty->display("vw_commandes.tpl");
