<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Fabien Ménager
 */
 
global $can;
$can->needsEdit();

$bloc_id = mbGetValueFromGetOrSession("bloc_id", 0);

// Récupération des blocs de l'etablissement
$blocs_list = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// Récupération du bloc à modifier
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("blocs_list", $blocs_list);
$smarty->assign("bloc",       $bloc);

$smarty->display("vw_idx_blocs.tpl");

?>