<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision: $
* @author Sherpa
*/

global $can;
$can->needsRead();

// Chargement du malade courant
$malade = new CSpMalade;
$malade->load(mbGetAbsValueFromGetOrSession("malnum"));
$malade->loadRefs();

// Chargement de tous les malades
$malades = $malade->loadList();
foreach ($malades as &$_malade) {
  $_malade->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("malade", $malade);
$smarty->assign("malades", $malades);

$smarty->display("view_malades.tpl");

?>