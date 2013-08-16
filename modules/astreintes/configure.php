<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage shortMessageService
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


CCanDo::Admin();

$smarty = new CSmartyDP();
$smarty->display("configure.tpl");