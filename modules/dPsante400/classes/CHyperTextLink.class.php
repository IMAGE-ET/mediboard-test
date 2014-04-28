<?php

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage dPsante400
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */
 
/**
 * Description
 */
class CHyperTextLink extends CMbMetaObject {
  /**
   * @var integer Primary key
   */
  public $hypertext_link_id;

  /**
   * The name of the link
   * @var string
   */
  public $name;

  /**
   * The hypertext link
   * @var string
   */
  public $link;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "hypertext_link";
    $spec->key    = "hypertext_link_id";
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
    $props['name'] = 'str notNull';
    $props['link'] = 'uri notNull';
    return $props;
  }
}
