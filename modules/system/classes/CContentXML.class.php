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
 * XML content
 */
class CContentXML extends CMbObject {
  public $content_id;
  
  // DB Fields
  public $content;
  public $import_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'content_xml';
    $spec->key   = 'content_id';
    $spec->loggable = false;
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() { 
    $props = parent::getProps();
    $props["content"]   = "xml show|0";
    $props["import_id"] = "num";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["messages"]            = "CEchangeHprim message_content_id";
    $backProps["acquittements"]       = "CEchangeHprim acquittement_content_id";
    $backProps["messages_phast"]      = "CExchangePhast message_content_id";
    $backProps["acquittements_phast"] = "CExchangePhast acquittement_content_id";
    return $backProps;
  }
}
