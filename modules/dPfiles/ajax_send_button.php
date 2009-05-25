<?php /* $Id: httpreq_check_file_integrity.php 6135 2009-04-21 10:49:02Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 6135 $
* @author Thomas Despoix
*/

global $can;
$can->needsEdit();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("notext", "");
$smarty->assign("_doc_item", mbGetObjectFromGet(null, null, "item_guid"));
$smarty->assign("onComplete", mbGetValueFromGet("onComplete"));

$smarty->display("inc_file_send_button.tpl");

?>