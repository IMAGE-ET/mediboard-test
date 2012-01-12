<?php

/**
 * Content Tabular
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CContentTabular extends CMbObject {
  // DB Table key
  var $content_id = null;
  
  // DB Fields
  var $content   = null;
  var $import_id = null;
  var $separator = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'content_tabular';
    $spec->key   = 'content_id';
    $spec->loggable = false;
    return $spec;
  }
  
  function getProps() { 
    $specs = parent::getProps();
    $specs["content"]   = "text show|0";
    $specs["import_id"] = "num";
    $specs["separator"] = "str length|1";
    
    return $specs;
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
