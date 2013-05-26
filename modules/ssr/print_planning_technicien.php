<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCando::checkRead();
$kine_id = CValue::get("kine_id");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("kine_id", $kine_id);
$smarty->display("print_planning_technicien.tpl");
