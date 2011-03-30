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
$source_ldap->loadObject();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("source_ldap", $source_ldap);
$smarty->display("configure.tpl");

?>