<?php

/**
 * $Id$
 *  
 * @category DMP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Management of OID
 */
class CMbOID {

  static $delimiter     = "1";
  static $class_mappage = array(
    "CCompteRendu" => "1", "CFile" => "2", "CPatient" => "3",
    "CMediusers" => "4", "CGroups" => "5", "CXDSRegistryPackage" => "6",
    "CExchangeHL7v3" => "7");

  /**
   * Return the instance OID
   *
   * @return String
   */
  static function getOIDRoot() {
    return CAppUI::conf("mb_oid");
  }

  /**
   * Return the instance OID
   *
   * @param CMbObject $class Class
   *
   * @return string
   */
  static function getOIDOfInstance($class) {
    $delimiter = self::$delimiter;
    $oid_root  = self::getOIDRoot();
    $oid_group = self::getGroupId($class);
    return $oid_root.".".$delimiter.".".$oid_group;
  }

  /**
   * Return the group Id
   *
   * @param CMbObject $class Class
   *
   * @return string
   */
  static function getGroupId($class) {
    $object = null;
    $result = null;
    if ($class instanceof CFile || $class instanceof CCompteRendu) {
      /** @var CCompteRendu|CFile $class */
      $class = $object = $class->loadTargetObject();
    }
    if ($class instanceof CConsultAnesth) {
      /** @var CConsultAnesth $class */
      $class = $class->loadRefConsultation();
    }

    switch (get_class($class)) {
      case "CMediusers":
      /** @var CMediusers $class */
      $result = $class->_group_id;
      break;
      case "CSejour":
        /** @var CSejour $class */
        $result = $class->group_id;
        break;
      case "COperation":
        /** @var COperation $class */
        $result = $class->loadRefSejour()->group_id;
        break;
      case "CConsultAnesth":
        /** @var CConsultAnesth $class */
        $result = $class->loadRefConsultation()->loadRefGroup()->group_id;
        break;
      case "CConsultation";
        /** @var CConsultation $class */
        $result = $class->loadRefGroup()->group_id;
        break;
      case "CPatient":
        /** @var CPatient $class */
        $result = "0";
        break;
      case "CGroups":
        /** @var CGroups $class */
        $result = $class->_id;
        break;
      case "CXDSRegistryPackage":
        /** @var CXDSRegistryPackage $class */
        $result = $class->_group_id;
        break;
      case "CExchangeHL7v3":
        /** @var CExchangeHL7v3 $class */
        $result = $class->group_id;
    }

    return $result;
  }

  /**
   * Return the class OID
   *
   * @param CMbObject $class Class
   *
   * @return string
   */
  static function getOIDFromClass($class) {
    $oid_instance = self::getOIDOfInstance($class);
    $delimiter    = self::$delimiter;
    $oid          = self::$class_mappage[get_class($class)];
    return $oid_instance.".".$delimiter.".".$oid;
  }
}