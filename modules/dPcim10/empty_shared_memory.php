<?php 

/**
 * $Id$
 *  
 * @category CCAM
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$classes = array(
  "CCodeCIM10",
);

foreach ($classes as $_class) {
  $count = SHM::remKeys("$_class*");
  CAppUI::stepAjax("module-system-msg-cache-removal", UI_MSG_OK, $count, $_class);
}
