<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

// Chargement de la liste des icones presents dans le fichier
$icones = CAppUI::readFiles("modules/dPcabinet/images/categories", ".png");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("icones", $icones);
$smarty->display("icone_selector.tpl");

?>