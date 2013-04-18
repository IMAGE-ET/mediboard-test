<?php

/**
 * Modify or create a pop source
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 
CCanDo::checkEdit();

$source_id = CValue::get("source_id");
$user = CMediusers::get();

$source = new CSourcePOP();
$source->_id = $source_id;
$source->load();

//new connexion
if (!$source->_id) {
  $source = new CSourcePOP();
  $source->object_class = $user->_class;
  $source->object_id = $user->_id;
  $number = $source->countMatchingList();
  $source->name = "SourcePOP-".$user->_id.'-'.($number+1);
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("source", $source);
$smarty->display("inc_vw_edit_sourcePOP.tpl");