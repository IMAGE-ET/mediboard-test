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

$domain_id = CValue::getOrSession("domain_id", null);

// Liste des domaines
$domain  = new CDomain();
/** @var CDomain[] $domains */
$domains = $domain->loadList();
foreach ($domains as $_domain) {
  $_domain->loadRefActor();
  $_domain->loadRefIncrementer()->loadView();  
  $_domain->loadRefsGroupDomains();
  foreach ($_domain->_ref_group_domains as $_group_domain) {
    $_group_domain->loadRefGroup();
  }
  $_domain->isMaster();
  if ($_domain->_id === $domain_id) {
    $domain = $_domain;
  }
}

// Liste des acteurs
$actor  = new CInteropActor(); 
$actors = $actor->getObjects();

// Récupération de l'incrementeur à ajouter/editer
if (!$domain->_id) {
  $domain->loadRefActor();
  $domain->loadRefIncrementer()->loadView();
  $domain->loadRefsGroupDomains();
  foreach ($domain->_ref_group_domains as $_group_domain) {
    $_group_domain->loadRefGroup();
  }
}

$groups = CGroups::loadGroups();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("domains"     , $domains);
$smarty->assign("domain"      , $domain);
$smarty->assign("actors"      , $actors);
$smarty->assign("groups"      , $groups);
$smarty->display("vw_domains.tpl");

