<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

$line_id = mbGetValueFromGet("line_id");
$line_class = mbGetValueFromGet("line_class");

// Chargement de la line
$line = new $line_class;
$line->load($line_id);

// Chargement des parents lines
$parent_lines = $line->loadRefsParents();
ksort($parent_lines);

foreach($parent_lines as &$parent_line){
	$parent_line->loadRefsPrises();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("parent_lines", $parent_lines);
$smarty->display("view_historique.tpl");

?>