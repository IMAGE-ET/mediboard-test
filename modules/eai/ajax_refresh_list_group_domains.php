<?php 

/**
 * View interop actors EAI
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

// Domaine
$domain = new CDomain();
$domain->load($domain_id);
$domain->loadRefsGroupDomains();
foreach ($domain->_ref_group_domains as $_group_domain) {
  $_group_domain->loadRefGroup();  
}

$group_domain = new CGroupDomain();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("domain"      , $domain);
$smarty->assign("group_domain", $group_domain);
$smarty->display("inc_vw_group_domains.tpl");

