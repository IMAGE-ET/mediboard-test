<?php /* $Id: vw_compta.php 1738 2007-03-19 16:33:47Z maskas $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1738 $
* @author Thomas Despoix
*/

global $AppUI, $can, $m;

$can->edit &= $AppUI->user_prefs["GestionFSE"];
$can->needsEdit();

$intermaxFunctions = array (
  "Configuration",
  "Professionnels de santé",
  "Lire Vitale",
  "Lire CPS",
  "Formater FSE",
  );
  
  
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("intermaxFunctions", $intermaxFunctions);
$smarty->display("vw_intermax.tpl");

