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

$domain_id = CValue::getOrSession("domain_id");

// Liste des domaines
$domain  = new CDomain();
$domains = $domain->loadList();
foreach ($domains as $_domain) {
  $_domain->loadRefActor();
  $_domain->loadRefIncrementer()->loadView();  
  $_domain->loadRefsGroupDomains();
  foreach ($_domain->_ref_group_domains as $_group_domain) {
    $_group_domain->loadRefGroup();  
  }
  $_domain->isMaster();
  $_domain->countObjects();
}

// Liste des acteurs
$actor  = new CInteropActor(); 
$actors = $actor->getObjects();

// Récupération due l'incrementeur à ajouter/editer 
$domain      = new CDomain();
$domain->_id = $domain_id;

$group_domain = new CGroupDomain();

$groups = CGroups::loadGroups();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("domains"     , $domains);
$smarty->assign("domain"      , $domain);
$smarty->assign("actors"      , $actors);
$smarty->assign("group_domain", $group_domain);
$smarty->assign("groups"      , $groups);
$smarty->display("vw_domains.tpl");

?>