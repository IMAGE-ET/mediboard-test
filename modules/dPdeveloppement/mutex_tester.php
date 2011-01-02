<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author Thomas Despoix
 */

CCanDo::checkRead();

$actions = array(
  "stall",
  "die",
  "run",
  "dummy",
);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("actions", $actions);

$smarty->display("mutex_tester.tpl");
?>