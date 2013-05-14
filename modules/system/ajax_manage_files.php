<?php

/**
 * $Id$
 *
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Check params
$source_guid = CValue::get("source_guid");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("source_guid", $source_guid);

$smarty->display("inc_manage_files.tpl");