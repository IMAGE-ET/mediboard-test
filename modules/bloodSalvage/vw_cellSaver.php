<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global  $can;
$can->needsRead();

$cell_saver_id = mbGetValueFromGetOrSession("cell_saver_id");

$cell_saver = new CCellSaver();
$cell_saver_list = $cell_saver->loadList(); 
if($cell_saver_id) {
  $cell_saver = new CCellSaver();
  $cell_saver->load($cell_saver_id);
}
$smarty = new CSmartyDP();

$smarty->assign("cell_saver_list", $cell_saver_list);
$smarty->assign("cell_saver",$cell_saver);
$smarty->display("vw_cellSaver.tpl");
?>