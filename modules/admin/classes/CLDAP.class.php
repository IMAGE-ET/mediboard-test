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
    $source_ldap = self::bind($user);
    
    // Logging succesfull
    if ($user->_bound) {
      $user = self::searchAndMap($user, $source_ldap, $source_ldap->_ldapconn);
      $user->_bound = true;
    }

    return $user;
  }

  static function bind(CUser $user = null, $ldaprdn = null, $ldappass = null) {
    $source_ldap = new CSourceLDAP();
    $source_ldap->loadObject();
    
    if (!$source_ldap->_id) {
      throw new CMbException("CSourceLDAP_undefined");
    }
    
    $ldapconn = $source_ldap->ldap_connect();
    $source_ldap->_ldapconn = $ldapconn;
    
    if (!$ldaprdn) {
      $ldaprdn  = $user->user_username;
    }
    if (!$ldappass) {
      $ldappass = $user->_user_password;
    }

    $bound = $source_ldap->ldap_bind($ldapconn, $ldaprdn, $ldappass);
    if ($user) {
      $user->_bound = $bound;
    }
    
    return $source_ldap;
  }
  
  static function searchAndMap(CUser $user, CSourceLDAP $source_ldap, $ldapconn, $person = null, $filter = null, $force_create = false) {
    if (!$person) {
      $person = $user->user_username;
    }
    $person = utf8_encode($person);
    if (!$filter) {
      $filter="(samaccountname=$person)";
    }

    $results = $source_ldap->ldap_search($ldapconn, $filter);

    if ($results["count"] > 1) {
      throw new CMbException("CSourceLDAP_too-many-results");
    }
    
    $results = $results[0];
    
    $id400               = new CIdSante400();
    $id400->tag          = CAppUI::conf("admin LDAP ldap_tag");
    $id400->object_class = "CUser";
    
    $id400->id400        = self::getObjectGUID($results);
    $id400->loadMatchingObject();
    // objectguid retrouvé on charge le user
    if ($id400->_id) {
      $user = new CUser();
      $user->load($id400->object_id);
    } 
    // objectguid non retrouvé on associe à l'user courant l'objectguid 
    else {
      // Si on est pas en mode création on le recherche
      if (!$force_create) {
        // Suppression du password pour le loadMatchingObject
        $user_password  = $user->user_password;
        $_user_password = $user->_user_password;
        $user->user_password  = null;
        $user->_user_password = null;
        
        $user->loadMatchingObject();
        if (!$user->_id) {
          throw new CMbException("Auth-failed-user-unknown");
        }
        
        $user->user_password  = $user_password;
        $user->_user_password = $_user_password;
      }
    }   
    $user = self::mapTo($user, $results);
    
    // Save Mediuser variables
    $actif        = $user->_user_actif;
    $deb_activite = $user->_user_deb_activite;
    $fin_activite = $user->_user_fin_activite;
    
    if (!$user->user_type) {
      $user->user_type = 0;
    }
    // Pas de profil
    $user->template = 0;
    $user->user_login_errors = 0;
    $user->repair();
    $msg = $user->store();
    if ($msg) {
      throw new CMbException($msg);
    }
   
    if ((!$force_create && !$user->_ref_mediuser->actif) || ($force_create && !$actif)) {
      throw new CMbException("Auth-failed-user-deactivated");
    }
    
    // Restore Mediuser variables
    $user->_user_actif = $actif;
    $user->_user_deb_activite = $deb_activite;
    $user->_user_fin_activite = $fin_activite;
    
    if (!$id400->_id) {
      $id400->object_id   = $user->_id;
      $id400->last_update = mbDateTime();
      $msg = $id400->store();
      if ($msg) {
        throw new CMbException($msg);
      }
    }
        
    return $user;
  }
  
  static function getValue($values = array(), $name, $single = true, $utf8_decode = true) {
    if (array_key_exists($name, $values)) {
      
      return $single ? 
              ($utf8_decode ? utf8_decode($values[$name][0]) : $values[$name][0]) : 
              ($utf8_decode ? utf8_decode($values[$name]) : $values[$name]);
    }
  }
  
  static function mapTo(CUser $user, $values) {
    $user->user_username   = self::getValue($values, "samaccountname");
    $user->user_first_name = self::getValue($values, "givenname");
    $user->user_last_name  = self::getValue($values, "sn") ? self::getValue($values, "sn") : self::getValue($values, "samaccountname");
    $user->user_phone      = self::getValue($values, "telephonenumber");
    $user->user_email      = self::getValue($values, "mail");
    $whencreated = null;
    if ($when_created = self::getValue($values, "whencreated")) {
      $whencreated      = mbDate(mbDateTimeFromAD($when_created));
    }
    $accountexpires = null;
    if ($account_expires = self::getValue($values, "accountexpires")) {
      // 1000000000000000000 = 16-11-4769 01:56:35
      if ($account_expires < 1000000000000000000) {
        $accountexpires = mbDate(mbDateTimeFromLDAP($account_expires));
      }
    }
    // 66048 = Enabled
    // 66050 = Disabled
    $actif = (self::getValue($values, "useraccountcontrol") == 66048) ? 1 : 0;
    $user->loadRefMediuser();
    if ($user->_id) {
      $mediuser = $user->_ref_mediuser;
      $mediuser->actif =  $actif;
      $mediuser->deb_activite = $whencreated;
      $mediuser->fin_activite = $accountexpires;
      $mediuser->store();
    }
    $user->_user_actif        = $actif;
    $user->_user_deb_activite = $whencreated;
    $user->_user_fin_activite = $accountexpires;
    
    return $user;
  }
  
  static function getObjectGUID($values) {
    // Passage en hexadécimal de l'objectguid
    $objectguid = unpack('H*', self::getValue($values, "objectguid", true, false));
    $objectguid = $objectguid[1];
    
    if (CAppUI::conf("admin LDAP object_guid_mode") == "registry") {
      $first_segment  = substr($objectguid, 4, 4);
      $second_segment = substr($objectguid, 0, 4);
      $third_segment  = substr($objectguid, 8, 4);
      $fourth_segment = substr($objectguid, 12, 4);
      $fifth_segment  = substr($objectguid, 16, 16);
      
      $first_segment  = implode("", array_reverse(str_split($first_segment, 2)));
      $second_segment = implode("", array_reverse(str_split($second_segment, 2)));
      $third_segment  = implode("", array_reverse(str_split($third_segment, 2)));
      $fourth_segment = implode("", array_reverse(str_split($fourth_segment, 2)));
    
      $objectguid = "$first_segment$second_segment-$third_segment-$fourth_segment-$fifth_segment";
    }
    
    return $objectguid;
  }
  
  static function getFromLDAPGuid($ldap_guid) {
    if (!$ldap_guid) {
      throw new CMbException("CUser_no-ldap-guid");
    }
    
    $id400               = new CIdSante400();
    $id400->object_class = "CUser";
    $id400->tag          = CAppUI::conf("admin LDAP ldap_tag");
    $id400->id400        = $ldap_guid;
    $id400->loadMatchingObject();
    if (!$id400->_id) {
      throw new CMbException("CUser_ldap-guid-no-user");
    } 
    $user = new CUser();
    
    return $user->load($id400->object_id);
  }
}

?>
