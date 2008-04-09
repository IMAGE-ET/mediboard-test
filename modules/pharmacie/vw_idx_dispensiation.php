<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$dispensiation_id = mbGetValueFromGetOrSession('dispensiation_id');

$dispensiation = new CDispensiation();
$dispensiation->load($dispensiation_id);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('dispensiation', $dispensiation);

$smarty->display('vw_idx_dispensiation.tpl');

?>