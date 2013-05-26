<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Any content
 */
class CContentAny extends CMbObject {
  public $content_id;
  
  // DB Fields
  public $content;
  public $import_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'content_any';
    $spec->key   = 'content_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() { 
    $props = parent::getProps();
    $props["content"]   = "text show|0";
    $props["import_id"] = "num";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["messages_generique"]      = "CExchangeAny message_content_id";
    $backProps["acquittements_generique"] = "CExchangeAny acquittement_content_id";
    $backProps["usermail_plain"]          = "CUserMail text_plain_id";
    return $backProps;
  }
}
