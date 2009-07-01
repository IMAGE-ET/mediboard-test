<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

mbTrace("Start");
$do = new CDoObjectAddEdit("CFichePaie", "fiche_paie_id");
$do->redirect = null;
$do->doIt();
mbTrace("End");

$fichePaie = new CFichePaie();
$fichePaie->load($do->_obj->_id);
$fichePaie->loadRefsFwd();
$fichePaie->_ref_params_paie->loadRefsFwd();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("fichePaie" , $fichePaie);

$fichePaie->final_file = $smarty->fetch("print_fiche.tpl");
mbTrace($fichePaie->store());
CApp::rip();

?>