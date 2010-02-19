<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;
$can->needsAdmin();

$status = 0;
$evenements = array(
  'evt_serveuractes' => array("1.01", "1.05"),
  'evt_pmsi'         => array("1.01", "1.05"),
  'evt_patients'     => array("1.05", "1.051"),
  'evt_mvtStock'     => array("1.01", "1.02"),
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("status"    , $status);
$smarty->assign("evenements", $evenements);

$smarty->display("configure.tpl");
?>