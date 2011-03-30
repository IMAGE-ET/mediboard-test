<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$source_ldap_id = CValue::get("source_ldap_id");

$source_ldap = new CSourceLDAP();
$source_ldap->load($source_ldap_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("source_ldap", $source_ldap);
$smarty->display("inc_source_ldap.tpl");

?>