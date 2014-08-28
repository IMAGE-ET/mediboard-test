<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$fs = new CFirstNameAssociativeSex();
$fs_id = CValue::get('fs_id');
$fs->load($fs_id);

//smarty
$smarty = new CSmartyDP();
$smarty->assign("object", $fs);
$smarty->display("inc_edit_firstname.tpl");
