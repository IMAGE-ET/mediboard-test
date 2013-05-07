<?php

/**
 * $Id$
 *
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */
 
 
class CMbPhone {

  /**
   * transform a phone number to an internation format
   *
   * @param string $phonenumber the phone number
   *
   * @return string $phonenumber the phone internationalized
   */
  static function phoneToInternational($phonenumber) {

    //french default format
    if (self::checkMobileNumber($phonenumber)) {
      return preg_replace("/0/", "33", $phonenumber, 1);
    }

    return $phonenumber;
  }

  /**
   * check for mobile number (06..., 07...)
   *
   * @param string $number  the number
   * @param string $country the country to check
   *
   * @return bool
   */
  static function checkMobileNumber($number, $country ="fr") {
    $phones = array(
      "fr"  => "/(0|\+33\s?)(6|7)(\s?\d{2}){4}/"
    );

    if (!array_key_exists($country, $phones)) {
      return false;
    }

    switch ($country) {
      case "fr":
        if (preg_match($phones[$country], $number)) {
          return true;
        }
        break;
    }

    return false;
  }


  /**
   * get the mobine phone of an user object
   *
   * @param string $guid MB guid
   *
   * @return string
   */
  static function getMobilePhoneFromGuid($guid) {
    $object = CMbObject::loadFromGuid($guid);
    $object->updateFormFields();

    if ($object instanceof CPerson) {
      return $object->_pmobilePhoneNumber;
    }

    return null;
  }
}