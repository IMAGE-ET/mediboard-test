<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$http_redirection_id = CValue::getOrSession("http_redirection_id");

// Récupération de la redirection à ajouter/éditer
$http_redirection = new CHttpRedirection();
$http_redirection->load($http_redirection_id);

// Récupération de la liste des redirections
$http_redirections = $http_redirection->loadList(null, "priority DESC");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("http_redirection",  $http_redirection);
$smarty->assign("http_redirections", $http_redirections);

$smarty->display("vw_idx_redirections.tpl");
