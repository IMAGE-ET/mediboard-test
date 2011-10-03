<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$source_ldap = new CSourceLDAP();
$sources_ldap = $source_ldap->loadList(null, "priority DESC");

$sources_ldap[] = $source_ldap; // to create a new one

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sources_ldap", $sources_ldap);
$smarty->display("inc_sources_ldap.tpl");

?>