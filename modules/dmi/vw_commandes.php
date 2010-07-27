<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", mbDate());
$type = CValue::getOrSession("type");

$dmi_line = new CPrescriptionLineDMI();
$dmi_line->date = $date;
$dmi_line->type = $type;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dmi_line"        , $dmi_line);
$smarty->display("vw_commandes.tpl");
