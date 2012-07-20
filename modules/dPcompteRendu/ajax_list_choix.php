<?php /* $Id: vw_idx_listes.php 12241 2011-05-20 10:29:53Z flaviencrochard $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 12241 $
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

// Liste sélectionnée
$liste_id = CValue::getOrSession("liste_id");
$liste = new CListeChoix();
$liste->load($liste_id); 

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("liste", $liste);

$smarty->display("inc_list_choix.tpl");

?>