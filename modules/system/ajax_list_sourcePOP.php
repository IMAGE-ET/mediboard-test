<?php
/**
 * list of user linked source POP
 *
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
 
$mediuser = CMediusers::get();
//smarty
$smarty = new CSmartyDP();
$smarty->assign("pop_source", $pop_sources);
$smarty->display("inc_vw_list_sourcesPOP.tpl");