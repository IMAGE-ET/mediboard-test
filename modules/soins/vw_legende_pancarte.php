<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage soins
* @version $Revision: $
* @author Alexis Granger
*/


// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("prescription", new CPrescription());
$smarty->display('vw_legende_pancarte.tpl');

?>

