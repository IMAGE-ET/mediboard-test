<?php 

/**
 * $Id$
 *  
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();

$smarty = new CSmartyDP();
$smarty->display("vw_search.tpl");