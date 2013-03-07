<?php /* $Id: httpreq_vw_hospi.php 12302 2011-05-27 13:08:17Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision: 12302 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();
$ds = CSQLDataSource::get("std");
// Récupération des paramètres
$chirSel   = CValue::getOrSession("chirSel");
$date      = CValue::getOrSession("date", CMbDT::date());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"   , $date);
$smarty->assign("chirSel", $chirSel);

$smarty->display("inc_worklist.tpl");
 
?>
