<?php 

/**
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$parturiente_id   = CValue::get("parturiente_id");
$large_icon       = CValue::get("large_icon");
$submit           = CValue::get("submit");
$modify_grossesse = CValue::get("modify_grossesse");

$parturiente = new CPatient();
$parturiente->load($parturiente_id);

$parturiente->loadLastGrossesse();

$smarty = new CSmartyDP();

$smarty->assign("object"          , $parturiente);
$smarty->assign("patient"         , $parturiente);
$smarty->assign("large_icon"      , $large_icon);
$smarty->assign("submit"          , $submit);
$smarty->assign("modify_grossesse", $modify_grossesse);

$smarty->display("inc_input_grossesse.tpl");