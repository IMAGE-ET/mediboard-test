<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", CMbDT::date());

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date"     , $date);
$smarty->display("vw_list_interv.tpl");
