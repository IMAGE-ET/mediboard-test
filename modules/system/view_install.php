<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead && !$dialog) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Création du template
$smarty = new CSmartyDP();

$smarty->display("view_install.tpl");

?>