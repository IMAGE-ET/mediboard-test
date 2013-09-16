<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$supervision_timed_picture_id = CValue::getOrSession("supervision_timed_picture_id");

$picture = new CSupervisionTimedPicture();
$picture->load($supervision_timed_picture_id);
$picture->loadRefsNotes();
$picture->loadRefsFiles();

$smarty = new CSmartyDP();
$smarty->assign("picture",  $picture);
$smarty->display("inc_edit_supervision_timed_picture.tpl");
