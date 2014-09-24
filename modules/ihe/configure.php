<?php 

/**
 * $Id$
 *  
 * @category ihe
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$function  = new CFunctions();
$functions = $function->loadList();


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("functions", $functions);
$smarty->display("configure.tpl");