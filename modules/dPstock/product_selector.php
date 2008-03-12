<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI;

// Création du template
$smarty = new CSmartyDP();

$smarty->display('product_selector.tpl');

?>