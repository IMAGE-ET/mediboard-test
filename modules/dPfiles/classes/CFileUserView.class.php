<?php

/**
 * $Id$
 *  
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CFileUserView extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $view_id;

  public $user_id;
  public $file_id;
  public $read_datetime;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "files_user_view";
    $spec->key    = "view_id";
    return $spec;  
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"] = "ref class|CMediusers notNull";
    $props["file_id"] = "ref class|CFile notNull";
    $props["read_datetime"] = "dateTime notNull";
    return $props;
  }
}
