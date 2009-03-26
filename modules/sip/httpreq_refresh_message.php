<?php 
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
global $AppUI;

$echange_hprim_id         = mbGetValueFromGet("echange_hprim_id");
$echange_hprim_classname  = mbGetValueFromGet("echange_hprim_classname");

// Chargement de l'objet
$echange_hprim = new $echange_hprim_classname;
$echange_hprim->load($echange_hprim_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $echange_hprim);

$smarty->display("inc_echange_hprim.tpl");

?>