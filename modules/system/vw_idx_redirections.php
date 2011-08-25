<?php /* $Id: view_messages.php 10359 2010-10-12 16:30:43Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10359 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
