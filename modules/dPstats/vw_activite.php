<?php /* $Id: vw_activite.php,v 1.6 2006/04/21 16:55:51 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1.6 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->display('vw_activite.tpl');

?>