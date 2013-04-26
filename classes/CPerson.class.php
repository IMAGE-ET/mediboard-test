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
 * Description
 */
class CPerson extends CMbObject {

  public $_pcity;
  public $_ppostalCode;
  public $_pstreetAddress;
  public $_pcountry;
  public $_pphoneNumber;
  public $_pfaxNumber;
  public $_pmobilePhoneNumber;
  public $_pemail;
  public $_pfirstName;
  public $_plastName;
  public $_pbirthDate;
  public $_pmaidenName;

  function getProps() {
    $props = parent::getProps();

    $props["_pcity"]              = "str notNull confidential seekable|begin";
    $props["_ppostalCode"]        = "str notNull seekable|begin";
    $props["_pstreetAddress"]     = "str";
    $props["_pcountry"]           = "str";
    $props["_pphoneNumber"]       = "str";
    $props["_pfaxNumber"]         = "str notNull confidential seekable|begin";
    $props["_pmobilePhoneNumber"] = "str notNull seekable|begin";
    $props["_pemail"]             = "str";
    $props["_pfirstName"]         = "str";
    $props["_plastName"]          = "str";
    $props["_pbirthDate"]         = "str";
    $props["_pmaidenName"]        = "str";

    return $props;
  }

  /**
   * Map the class variable with CPerson variable
   *
   * @return void
   */
  function mapPerson() {
  }
}