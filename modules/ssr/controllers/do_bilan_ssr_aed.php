<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$bilan = new CBilanSSR;
if ($bilan->sejour_id = CValue::post("sejour_id")) {
  if ($bilan->loadMatchingObject()) {
    $_POST["bilan_id"] = $bilan->_id;
  }
}

$do = new CDoObjectAddEdit("CBilanSSR");
$do->doIt();
