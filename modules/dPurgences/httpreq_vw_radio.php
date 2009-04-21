<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision$
* @author Alexis Granger
*/

// Chargement du rpu
$rpu_id = mbGetValueFromGetOrSession("rpu_id");
$rpu = new CRPU();
$rpu->load($rpu_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rpu"   , $rpu);
$smarty->display("inc_vw_radio.tpl");

?>