<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */



global $AppUI, $can, $m;



// Création du template
$smarty = new CSmartyDP();

$smarty->display("vw_idx_livret.tpl");

?>