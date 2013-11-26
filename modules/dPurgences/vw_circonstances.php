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


$circonstance = new CCirconstance();
$list_circonstances = $circonstance->loadList(null, "Code");

$smarty = new CSmartyDP();
$smarty->assign("list_circonstances", $list_circonstances);
$smarty->display("vw_circonstances.tpl");