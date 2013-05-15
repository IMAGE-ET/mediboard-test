<?php

/**
 * dPbloc
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$type_ressource_id = CValue::getOrSession("type_ressource_id");

$type_ressource = new CTypeRessource();
$type_ressource->group_id = CGroups::loadCurrent()->_id;

/** @var CTypeRessource[] $type_ressources */
$type_ressources = $type_ressource->loadMatchingList();

foreach ($type_ressources as $_type_ressource) {
  $_type_ressource->loadRefsRessources();
}

$smarty = new CSmartyDP;

$smarty->assign("type_ressource_id", $type_ressource_id);
$smarty->assign("type_ressources", $type_ressources);

$smarty->display("inc_list_type_ressources.tpl");
