<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $canAdmin, $m;

if(!$canAdmin) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$smarty = new CSmartyDP();
$smarty->display("vw_create_archive.tpl");
?>