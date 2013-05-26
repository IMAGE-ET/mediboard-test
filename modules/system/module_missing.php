<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mod", CValue::get("mod"));

$smarty->display("module_missing.tpl");
