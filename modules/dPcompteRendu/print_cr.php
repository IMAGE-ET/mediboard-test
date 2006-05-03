<?php /* $Id: print_cr.php,v 1.1 2005/10/11 07:05:14 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu') );

// Récupération du compte-rendu
$compte_rendu_id = dPgetParam($_GET, "compte_rendu_id", 0);

$compteRendu = new CCompteRendu;
$compteRendu->load($compte_rendu_id);

$cr = $compteRendu->source;

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('cr', $cr);

$smarty->display('print_cr.tpl');

?>