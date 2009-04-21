<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;
$can->needsAdmin();

$spClasses = array(
  "CSpMalade", 
  "CSpSejMed",
  "CSpEntCCAM",
  "CSpDetCCAM",
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("spClasses", $spClasses);
$smarty->display("configure.tpl");

?>