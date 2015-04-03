<?php

/**
 * Edit domain EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$domain_id = CValue::getOrSession("domain_id");

// Récupération du domaine à ajouter/editer 
$domain = new CDomain();
$domain->load($domain_id);
$domain->loadRefsGroupDomains();
foreach ($domain->_ref_group_domains as $_group_domain) {
  $_group_domain->loadRefGroup();  
}
$domain->loadRefActor();
$domain->loadRefIncrementer()->loadView();  
$domain->isMaster();

// Liste des acteurs
$actor = new CInteropActor(); 
$actors = $actor->getObjects();

$group_domain = new CGroupDomain();

$groups = CGroups::loadGroups();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("domain"      , $domain);
$smarty->assign("actors"      , $actors);
$smarty->assign("group_domain", $group_domain);
$smarty->assign("groups"      , $groups);
$smarty->display("inc_edit_domain.tpl");
