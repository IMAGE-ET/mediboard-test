<?php /* $Id: print_admission.php,v 1.3 2005/03/31 15:22:31 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision: 1.3 $
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

$id = mbGetValueFromGetOrSession("id");

$admission = new Coperation();
$admission->load($id);
$admission->loadRefs();
$admission->_ref_pat->loadRefs();

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('admission', $admission);

$smarty->display('print_admission.tpl');

?>