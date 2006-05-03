<?php /* $Id: print_pack.php,v 1.1 2005/04/14 16:28:23 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

// !! Attention, régression importante si ajout de type de paiement

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'templatemanager') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'pack') );

// Récupération des paramètres
$operation_id = dPgetParam($_GET, "operation_id", null);
$op = new COperation;
$op->load($operation_id);
$op->loadRefsFwd();
$patient =& $op->_ref_pat;

$pack_id = dPgetParam($_GET, "pack_id", null);

$pack = new CPack;
$pack->load($pack_id);

// Creation des template manager
$listCr = array();
foreach($pack->_modeles as $key => $value) {
  $listCr[$key] = new CTemplateManager;
  $listCr[$key]->valueMode = true;
  $op->fillTemplate($listCr[$key]);
  $patient->fillTemplate($listCr[$key]);
  $listCr[$key]->applyTemplate($value);
}

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;
$smarty->debugging = false;

$smarty->assign('listCr', $listCr);

$smarty->display('print_pack.tpl');

?>