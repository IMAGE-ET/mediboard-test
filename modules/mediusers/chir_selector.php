<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediuser
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

// get all authorized praticians

$spe  = CValue::get("spe" , 0  );
$name = CValue::get("name", "" );

$prats = new CMediusers;
$prats = $prats->loadPraticiens(PERM_EDIT, $spe, $name);

// get all authorized functions
$specs = new CFunctions;
$specs = $specs->loadSpecialites(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prats", $prats);
$smarty->assign("specs", $specs);
$smarty->assign("name" , $name);
$smarty->assign("spe"  , $spe);

$smarty->display("chir_selector.tpl");

?>