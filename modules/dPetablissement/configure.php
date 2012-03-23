<?php /* $Id: index.php 7320 2009-11-14 22:42:28Z lryo $ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$departements = range(1, 95);
foreach($departements as &$_departement) {
  $_departement = sprintf("%02d", $_departement);
}

$departements = array_merge($departements, array("2A", "2B", "9A", "9B", "9C", "9D", "9E", "9F"));

// Création du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");

?>