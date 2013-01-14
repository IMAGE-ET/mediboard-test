<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$ex_class_id = CValue::get("ex_class_id");

$ex_class = new CExClass;
$ex_class->load($ex_class_id);
$ex_class->loadRefsEvents();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_class", $ex_class);
$smarty->display("inc_ex_class_events_preview.tpl");

