<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$rpu_id = CValue::get("rpu_id");

$rpu = new CRPU;
$rpu->load($rpu_id);

$smarty = new CSmartyDP;
$smarty->assign("rpu" , $rpu);
$smarty->display("inc_edit_diag.tpl");
