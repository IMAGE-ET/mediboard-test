<?php /* $Id: configure.php 6067 2009-04-14 08:04:15Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dompdf_installed", file_exists("lib/dompdf/include/dompdf.cls.php"));
$smarty->display('configure.tpl');

?>