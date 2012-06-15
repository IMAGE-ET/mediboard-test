<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6518 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$box_urgences_id    = CValue::getOrSession("box_urgences_id");

$box = new CBoxUrgence();
$box->load($box_urgences_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("box"     , $box);

$smarty->display("inc_vw_box_urgence.tpl");
?>