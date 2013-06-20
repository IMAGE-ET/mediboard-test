<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage GestionCab
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

mbTrace("Start");
$do = new CDoObjectAddEdit("CFichePaie");
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
