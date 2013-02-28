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

class CContentTabular extends CMbObject {
  // DB Table key
  public $content_id;
  
  // DB Fields
  public $content;
  public $import_id;
  public $separator;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'content_tabular';
    $spec->key   = 'content_id';
    $spec->loggable = false;
    return $spec;
  }
  
  function getProps() { 
    $props = parent::getProps();
    $props["content"]   = "text show|0";
    $props["import_id"] = "num";
    $props["separator"] = "str length|1";
    
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["messages_hprim21"]      = "CEchangeHprim21 message_content_id";
    $backProps["acquittements_hprim21"] = "CEchangeHprim21 acquittement_content_id";
    $backProps["messages_ihe"]          = "CExchangeIHE message_content_id";
    $backProps["acquittements_ihe"]     = "CExchangeIHE acquittement_content_id";
    return $backProps;
  }
}
