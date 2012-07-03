<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$smarty = new CSmartyDP();
$smarty->assign("fiche", new CFicheEi);
$smarty->display("vw_legende_criticite.tpl"); 

?>