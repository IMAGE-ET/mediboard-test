<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$ressource_id      = CValue::getOrSession("ressource_id");
$type_ressource_id = CValue::getOrSession("type_ressource_id");

$smarty = new CSmartyDP;

$smarty->assign("ressource_id"     , $ressource_id);
$smarty->assign("type_ressource_id", $type_ressource_id);

$smarty->display("vw_ressources.tpl");

?>