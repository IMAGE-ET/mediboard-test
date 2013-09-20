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

$timed_picture_id = CValue::get("timed_picture_id");

$tree = CMbPath::getTree(CSupervisionTimedPicture::PICTURES_ROOT);

$smarty = new CSmartyDP();
$smarty->assign("tree",  $tree);
$smarty->assign("timed_picture_id",  $timed_picture_id);
$smarty->display("inc_vw_supervision_pictures.tpl");
