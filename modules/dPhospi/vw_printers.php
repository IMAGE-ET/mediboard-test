<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$printer_id = CValue::getOrSession("printer_id", 0);

$smarty = new CSmartyDP();
$smarty->assign("printer_id", $printer_id);
$smarty->display("vw_printers.tpl");

