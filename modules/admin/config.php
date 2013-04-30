<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["admin"] = array (
  "CUser" => array(
    "strong_password"         => "1",
    "apply_all_users"         => "0",
    "max_login_attempts"      => "5",
    "allow_change_password"   => "1",
    "force_changing_password" => "0",
    "password_life_duration"  => "3 month",
  ),
  "LDAP" => array(
    "ldap_connection"         => "0",
    "ldap_tag"                => "ldap",
    "ldap_user"               => "",
    "ldap_password"           => "",
    "object_guid_mode"        => "hexa",
    "allow_change_password"   => "0",
    "allow_login_as_admin"    => "0"
  ),
);

?>