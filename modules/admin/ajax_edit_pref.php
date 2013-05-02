<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$pref_id = CValue::getOrSession("pref_id");

$preference = new CPreferences();
$preference->load($pref_id);
$preference->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("preference", $preference);
$smarty->display("inc_edit_pref.tpl");
