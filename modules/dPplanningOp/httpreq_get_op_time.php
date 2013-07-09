<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$chir_id = CValue::get("chir_id" , 0 );
$codes   = CValue::get("codes"   , "");
$javascript = CValue::get("javascript" , true);

$codes = explode("|", $codes);
$result = CTempsOp::getTime($chir_id, $codes);
$temps = $result ? strftime("%Hh%M", $result) : $temps = "-";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("temps", $temps);
$smarty->assign("javascript", $javascript);

$smarty->display("inc_get_time.tpl");
