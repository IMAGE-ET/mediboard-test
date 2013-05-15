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

$type_ressource = new CTypeRessource;
$type_ressource->load($type_ressource_id);

if (!$type_ressource->_id) {
  $type_ressource->group_id = CGroups::loadCurrent()->_id;
}

$smarty = new CSmartyDP;

$smarty->assign("type_ressource", $type_ressource);

$smarty->display("inc_edit_type_ressource.tpl");
