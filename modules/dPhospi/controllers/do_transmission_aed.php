<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */
/*
if(isset($_POST["date"]) && $_POST["date"] == "now") {
  $_POST["date"] = CMbDT::dateTime();
}
*/
$do = new CDoObjectAddEdit("CTransmissionMedicale", "transmission_medicale_id");
$do->doIt();