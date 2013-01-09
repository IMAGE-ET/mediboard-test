<?php

/**
 * Content Any
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CContentAny extends CMbObject {
  // DB Table key
  var $content_id = null;
  
  // DB Fields
  var $content   = null;
  var $import_id = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'content_any';
    $spec->key   = 'content_id';
    return $spec;
  }
  
  function getProps() { 
    $specs = parent::getProps();
    $specs["content"]   = "text show|0";
    $specs["import_id"] = "num";
    
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["messages_generique"]      = "CExchangeAny message_content_id";
    $backProps["acquittements_generique"] = "CExchangeAny acquittement_content_id";
    $backProps["usermail_plain"] = "CUserMail text_plain_id";

    return $backProps;
  }
}
