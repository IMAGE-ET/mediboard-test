<?php 

/**
 * Refresh incrementer/actor EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkAdmin();

$domain_id = CValue::get("domain_id");

// Liste des domaines
$domain  = new CDomain();
$domain->load($domain_id);
$domain->loadRefsGroupDomains();
$domain->loadRefActor();
$domain->loadRefIncrementer()->loadView();  
$domain->isMaster();

// Liste des acteurs
$actor = new CInteropActor(); 
$actors = $actor->getObjects();

$groups = CGroups::loadGroups();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("domain"      , $domain);
$smarty->assign("actors"      , $actors);
$smarty->assign("groups"      , $groups);
$smarty->display("inc_vw_incrementer_actor.tpl");
