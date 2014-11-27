<?php

/**
 * Edit infos
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */


CCanDo::check();

$mediuser = CMediusers::get();
$mediuser->loadRefFunction();
$mediuser->loadRefSpecCPAM();
$mediuser->loadRefDiscipline();
$mediuser->_ref_user->isLDAPLinked();

// Récupération des disciplines
$disciplines = new CDiscipline();
$disciplines = $disciplines->loadList();

// Chargement des banques
$order = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null, $order);


// Récupération des spécialités CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList(null, 'spec_cpam_id ASC');

$affiche_nom = CValue::get("affiche_nom", 0);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("banques"    , $banques);
$smarty->assign("b2g"        , CAppUI::conf("admin CBrisDeGlace enable_bris_de_glace", CGroups::loadCurrent()));
$smarty->assign("disciplines", $disciplines);
$smarty->assign("spec_cpam"  , $spec_cpam);
$smarty->assign("fonction"   , $mediuser->_ref_function);
$smarty->assign("user"       , $mediuser);
$smarty->assign("affiche_nom", $affiche_nom);
$smarty->display("edit_infos.tpl");