<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimSanteMessageXPath
 * XPath HPR
 */
class CHPrimSanteMessageXPath extends CMbXPath {
  /**
   * @see parent::__construct()
   */
  function __construct(DOMDocument $dom) {
    parent::__construct($dom);

    $this->registerNamespace("hpr", "urn:hpr-org:v2xml");
  }

  /**
   * @see parent::convertEncoding
   */
  function convertEncoding($value) {
    return $value;
  }
}
