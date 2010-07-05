<?php /* $Id: configure.php 8400 2010-03-22 16:39:15Z lryo $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: 8400 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");
?>