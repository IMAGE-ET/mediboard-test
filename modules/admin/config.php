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
    "strong_password"         => "1",
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
  ),
);

?>