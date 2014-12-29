<?php 

/**
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$miner = new CDailySalleOccupation();
mbTrace($miner->countUnmined(), "unmined");
mbTrace($miner->countUnremined(), "un-remined");
mbTrace($miner->countUnpostmined(), "un-postmined");

$smarty = new CSmartyDP();
$smarty->display("inc_mine_salle.tpl");