<?php /* $Id: configure.php,v 1.1 2005/05/03 21:06:34 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->display("configure.tpl");

?>