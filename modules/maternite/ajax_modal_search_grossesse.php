<?php 

/**
 * $Id$
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$lastname = CValue::get("lastname");

$smarty = new CSmartyDP();
$smarty->assign("lastname", $lastname);
$smarty->display("inc_search_grossesse.tpl");