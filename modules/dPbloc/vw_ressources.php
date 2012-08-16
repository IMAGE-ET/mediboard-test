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

$indispo_ressource_id = CValue::getOrSession("indispo_ressource_id");
$type_ressource_id = CValue::getOrSession("type_ressource_id");
$date_indispo      = CValue::getOrSession("date_indispo");

$smarty = new CSmartyDP;

$smarty->assign("indispo_ressource_id", $indispo_ressource_id);
$smarty->assign("type_ressource_id", $type_ressource_id);
$smarty->assign("date_indispo", $date_indispo);

$smarty->display("vw_ressources.tpl");

?>