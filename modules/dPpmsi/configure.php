<?php /* $Id: configure.php,v 1.1 2006/04/05 18:19:39 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->display("configure.tpl");

?>