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

$domain_id       = CValue::getOrSession("domain_id");
$group_domain_id = CValue::getOrSession("group_domain_id");

// Récupération du domaine à ajouter/editer 
$domain = new CDomain();
$domain->load($domain_id);

// Récupération de l'établissement du domaine à editer 
$group_domain = new CGroupDomain();
$group_domain->load($group_domain_id);

$groups = CGroups::loadGroups();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("domain"      , $domain);
$smarty->assign("group_domain", $group_domain);
$smarty->assign("groups"      , $groups);
$smarty->display("inc_edit_group_domain.tpl");
