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

class CExchangeHTTP extends CExchangeTransportLayer {
  // DB Table key
  public $echange_http_id;
  
  // DB Fields
  public $http_fault;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_http';
    $spec->key   = 'echange_http_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["http_fault"] = "bool";
    
    return $props;
  }
  
}
