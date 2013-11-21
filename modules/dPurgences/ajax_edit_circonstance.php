<?php

/**
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$id = CValue::get("id");

$circonstance = new CCirconstance();
$circonstance->load($id);

$smarty = new CSmartyDP();
$smarty->assign("circonstance", $circonstance);
$smarty->display("inc_edit_circonstance.tpl");