<?php

/**
 * $Id: $
 *
 * Date types stubs
 *
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

// @codingStandardsIgnoreStart

/**
 * ISO Date: "YYYY-MM-DD"
 */
class date {
  private function __construct(){}

  function __toString() {
    return "0000-00-00";
  }
}

/**
 * ISO Time: "hh:mm:ss"
 */
class time {
  private function __construct(){}

  function __toString() {
    return "00:00:00";
  }
}

/**
 * ISO DateTime: "YYYY-MM-DD hh:mm:ss"
 */
class datetime {
  private function __construct(){}

  function __toString() {
    return "0000-00-00 00:00:00";
  }
}

/**
 * Class name
 */
class klass {
  private function __construct(){}

  function __toString() {
    return "stdClass";
  }
}

/**
 * CModelObject globally unique idenfifier
 */
class guid {
  private function __construct(){}

  function __toString() {
    return "stdClass-0";
  }
}

/**
 * CModelObject reference identifier
 */
class ref {
  private function __construct(){}

  function __toString() {
    return "0";
  }
}

// @codingStandardsIgnoreEnd

