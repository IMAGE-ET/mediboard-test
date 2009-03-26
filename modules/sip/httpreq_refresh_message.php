<?php 
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
global $AppUI;

$message_hprim_id         = mbGetValueFromGet("message_hprim_id");
$message_hprim_classname  = mbGetValueFromGet("message_hprim_classname");

// Chargement de l'objet
$message_hprim = new $message_hprim_classname;
$message_hprim->load($message_hprim_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $message_hprim);

$smarty->display("inc_message_hprim.tpl");

?>