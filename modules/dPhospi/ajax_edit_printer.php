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

$printer = new CPrinter();
$printer->load($printer_id);

if ($printer->_id) {
  $printer->loadTargetObject();
}

$source = new CSourceLPR();
$sources = $source->loadlist();

if (!$sources) {
  $sources = array();
}

$source = new CSourceSMB();
$sources_smb = $source->loadlist();

if ($sources_smb) {
  $sources = array_merge($sources, $sources_smb);
}

$function  = new CFunctions;
$order_by = "text";
$functions = $function->loadListWithPerms(PERM_READ, null, $order_by);

$smarty = new CSmartyDP();

$smarty->assign("printer"  , $printer);
$smarty->assign("sources"  , $sources);
$smarty->assign("functions", $functions);
$smarty->display("inc_edit_printer.tpl");

