<?php 

/**
 * Legend of the new planning
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */
 
 CCanDo::checkRead();

//smarty
$smarty = new CSmartyDP();
$smarty->display("vw_legend_planning_new.tpl");