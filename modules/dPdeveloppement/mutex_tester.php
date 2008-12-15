<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision:  $
 * @author Thomas Despoix
 */

global $can;
$can->needsRead();

$actions = array(
  "stall",
  "die",
  "run",
);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("actions", $actions);

$smarty->display("mutex_tester.tpl");
?>