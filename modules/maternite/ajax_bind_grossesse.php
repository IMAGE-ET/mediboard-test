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

$parturiente_id    = CValue::get("parturiente_id");
$object_guid       = CValue::get("object_guid");
$grossesse_id_form = CValue::getOrSession("grossesse_id_form");

$smarty = new CSmartyDP;

$smarty->assign("parturiente_id", $parturiente_id);
$smarty->assign("object_guid"   , $object_guid);
$smarty->assign("grossesse_id_form"  , $grossesse_id_form);

$smarty->display("inc_bind_grossesse.tpl");

?>
