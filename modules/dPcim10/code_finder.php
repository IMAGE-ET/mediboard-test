<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcim10', 'codecim10') );

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$code = dPgetParam($_GET, "code");

$cim10 = new CCodeCIM10($code);
$cim10->load();
$cim10->loadRefs();
foreach($cim10->_exclude as $key => $value) {
  $cim10->_exclude[$key]->loadRefs();
}
foreach($cim10->_levelsInf as $key => $value) {
  $cim10->_levelsInf[$key]->loadRefs();
}

$up = null;
$i = count($cim10->_levelsSup);
$i -= 1;
if($i >= 0)
  $up =& $cim10->_levelsSup[$i];

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('up', $up);
$smarty->assign('cim10', $cim10);

$smarty->display('code_finder.tpl');