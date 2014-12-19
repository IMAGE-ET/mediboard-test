<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage DICOM
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * DICOM message family
 */
class CDicomMessage extends CInteropNorm {
  /**
   * The constructor
   *
   * @return self
   */
  function __construct() {
    $this->name = "CDicomMessage";

    parent::__construct();
  }
}
