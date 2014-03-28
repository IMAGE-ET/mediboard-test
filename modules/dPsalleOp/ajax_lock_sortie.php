<?php 

/**
 * $Id$
 *  
 * @category SalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$curr_user = CMediusers::get();
$anesths = $curr_user->loadAnesthesistes();

$smarty = new CSmartyDP();

$smarty->assign("anesths", $anesths);

$smarty->display("inc_lock_sortie.tpl");