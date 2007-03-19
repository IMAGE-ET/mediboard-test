<?php /* $Id: import_orl.php 783 2006-09-14 12:44:01Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 783 $
* @author Thomas Despoix
*/

global $AppUI, $can, $m;

$can->needsRead();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("import_ami.tpl");

?>