<?php

/**
 * Onglet de configuration
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$smarty = new CSmartyDP();
$smarty->assign("start", CMbDT::date());
$smarty->assign("end", CMbDT::date());

$smarty->display("configure.tpl");
