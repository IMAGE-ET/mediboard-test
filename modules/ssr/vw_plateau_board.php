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

CCanDo::checkRead();

$plateau = new CPlateauTechnique;
$plateau->group_id = CGroups::loadCurrent()->_id;

// Plateaux disponibles
$plateaux_ids = array();
$plateaux = $plateau->loadMatchingList();
foreach ($plateaux as $_plateau) {
  $equipements = $_plateau->loadBackRefs("equipements");
  $plateaux_ids[$_plateau->_id] = array_keys($equipements);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plateaux", $plateaux);
$smarty->assign("plateaux_ids", $plateaux_ids);
$smarty->display("vw_plateau_board.tpl");
