<?php /* $Id: access_denied.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("module", mbGetValueFromGet("module"));

$smarty->display("module_missing.tpl");
?>