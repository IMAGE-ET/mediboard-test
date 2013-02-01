<?php

/**
 * tests for each contextual view
 *
 * @category Context
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

//smarty
$smarty = new CSmartyDP();
$smarty->display("vw_tests.tpl");