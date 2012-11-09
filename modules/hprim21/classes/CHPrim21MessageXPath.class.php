<?php
/**
 * $Id: CHPrim21MessageXPath.class.php 16236 2012-07-26 08:24:14Z phenxdesign $
 * 
 * @package    Mediboard
 * @subpackage hprim21
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16236 $
 */

/**
 * Class CHPrim21MessageXPath 
 * XPath HPR
 */
class CHPrim21MessageXPath extends CMbXPath {
  function __construct(DOMDocument $dom) {
    parent::__construct($dom);
    
    $this->registerNamespace("hpr", "urn:hpr-org:v2xml");
  }
  
  function convertEncoding($value) {
    return $value;
  }
}
