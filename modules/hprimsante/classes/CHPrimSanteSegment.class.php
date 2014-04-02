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
 * Hprim sante segment
 */
class CHPrimSanteSegment extends CHL7v2Segment {

  /**
   * Build an HL7v2 segment
   *
   * @param string             $name   The name of the segment
   * @param CHL7v2SegmentGroup $parent The parent of the segment to create
   *
   * @return CHL7v2Segment The segment
   */
  static function create($name, $parent) {
    $class = "CHPrimSanteSegment$name";

    if (class_exists($class)) {
      $segment = new $class($parent);
    }
    else {
      $segment = new self($parent);
    }

    $segment->name = $name;

    return $segment;
  }
}