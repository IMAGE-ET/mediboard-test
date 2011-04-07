<?php /* $Id: configure.php 6515 2009-06-30 09:58:59Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 6515 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["admin"] = array (
  "CUser" => array(
    "strong_password" => "0",
    "max_login_attempts" => "5",
  ),
  "LDAP" => array(
    "ldap_connection" => "0",
    "ldap_tag"        => "",
    "ldap_user"       => "",
    "ldap_password"   => "",
  ),
);

?>