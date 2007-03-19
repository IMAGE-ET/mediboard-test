<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

$object_classname = mbGetValueFromGet("object_classname");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_classname" , $object_classname);

$smarty->display("object_not_found.tpl");

?>