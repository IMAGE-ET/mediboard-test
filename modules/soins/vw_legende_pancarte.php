<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("prescription", new CPrescription());
$smarty->display('vw_legende_pancarte.tpl');

?>

