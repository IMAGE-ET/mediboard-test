<?php

/**
 * $Id$
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * class to harmonize person fields and represent a person
 */
class CPerson extends CMbObject {
  public $_p_city;
  public $_p_postal_code;
  public $_p_street_address;
  public $_p_country;
  public $_p_phone_number;
  public $_p_fax_number;
  public $_p_mobile_phone_number;
  public $_p_email;
  public $_p_first_name;
  public $_p_last_name;
  public $_p_birth_date;
  public $_p_maiden_name;

  /**
   * Get properties specifications as strings
   *
   * @see parent::getProps()
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["_p_city"]                = "str";
    $props["_p_postal_code"]         = "str";
    $props["_p_street_address"]      = "str";
    $props["_p_country"]             = "str";
    $props["_p_phone_number"]        = "phone";
    $props["_p_fax_number"]          = "phone";
    $props["_p_mobile_phone_number"] = "phone";
    $props["_p_email"]               = "str";
    $props["_p_first_name"]          = "str";
    $props["_p_last_name"]           = "str";
    $props["_p_birth_date"]          = "birthDate";
    $props["_p_maiden_name"]         = "str";

    return $props;
  }

  /**
   * return the sex field of the herited class
   *
   * @return null|string
   */
  function getSexFieldName() {
    return null;
  }

  /**
   * Map the class variable with CPerson variable
   *
   * @return void
   */
  function mapPerson() {
  }
}