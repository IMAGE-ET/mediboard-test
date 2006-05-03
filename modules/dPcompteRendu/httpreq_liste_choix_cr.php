<?php /* $Id: httpreq_liste_choix_cr.php,v 1.1 2006/05/01 15:40:42 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 1.1 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers'));
require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu'));
require_once( $AppUI->getModuleClass('dPcompteRendu', 'listeChoix'));
require_once( $AppUI->getModuleClass('dPcompteRendu', 'templatemanager'));

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$compte_rendu_id = dPgetParam($_GET, "compte_rendu_id", 0);

// Faire ici le test des différentes variables dont on a besoin

$compte_rendu = new CCompteRendu;
if($compte_rendu_id) {
  $compte_rendu->load($compte_rendu_id);
}
$compte_rendu->loadRefsFwd();
$compte_rendu->_ref_object->loadRefsFwd();
$object =& $compte_rendu->_ref_object;
$medichir =& $object->_ref_chir;

// Gestion du template
$templateManager = new CTemplateManager;

$templateManager->document = $compte_rendu->source;

$where = array();
$where[] = "(chir_id = '$medichir->user_id' OR function_id = '$medichir->function_id')";
$order = "chir_id, function_id";
$chirLists = new CListeChoix;
$chirLists = $chirLists->loadList($where, $order);
$lists = $templateManager->getUsedLists($chirLists);

$templateManager->initHTMLArea();

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));

$smarty = new CSmartyDP;

$smarty->assign('templateManager', $templateManager);
$smarty->assign('compte_rendu', $compte_rendu);
$smarty->assign('lists', $lists);

$smarty->display('inc_liste_choix_cr.tpl');

?>