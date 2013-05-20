<?php

/**
 * dPccam
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("childs_codable_class", CApp::getChildClasses("CCodable"));

$smarty->display("configure.tpl");
