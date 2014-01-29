<?php 

/**
 * $Id$
 *  
 * @category dPplanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$cpi      = new CChargePriceIndicator;
$list_cpi = $cpi->loadGroupList();

$mode_entree       = new CModeEntreeSejour();
$list_modes_entree = $mode_entree->loadGroupList();

$mode_sortie       = new CModeSortieSejour();
$list_modes_sortie = $mode_sortie->loadGroupList();


$smarty = new CSmartyDP();

$smarty->assign("list_cpi", $list_cpi);
$smarty->assign("list_modes_entree", $list_modes_entree);
$smarty->assign("list_modes_sortie", $list_modes_sortie);

$smarty->display("vw_parametrage.tpl");