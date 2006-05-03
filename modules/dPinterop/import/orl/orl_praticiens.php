<?php /* $Id: orl_praticiens.php,v 1.2 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 1.2 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

mbInsertCSV("modules/dPinterop/PRATICIEN.TXT", "import_praticiens");

?>