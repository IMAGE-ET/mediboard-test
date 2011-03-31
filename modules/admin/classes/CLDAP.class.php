<?php /* $Id: admin.class.php 11696 2011-03-29 14:07:58Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 11696 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CLDAP {
  static function login(CUser $user) {
    $source_ldap = new CSourceLDAP();
    $source_ldap->loadObject();
    
    if (!$source_ldap->_id) {
      throw new CMbException("CSourceLDAP_undefined");
    }
    
    try {
      $ldapconn = $source_ldap->ldap_connect();
    } catch(Exception $e) {
      throw $e; return ;
    }
    
    $ldaprdn  = $user->user_username;
    $ldappass = $user->_user_password;

    try {
      $bound = $source_ldap->ldap_bind($ldapconn, $ldaprdn, $ldappass);
    } catch(Exception $e) {
      throw $e; return false;
    }
    
    // Logging succesfull
    if ($bound) {
      $user->user_password  = null;
      $user->_user_password = null;
      $user->loadMatchingObject();
      $user->user_login_errors = 0;
      $user->store();
    }
    
    return $bound;
  }  
}

?>