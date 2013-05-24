<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

// Praticien courant pour les prises de rendez-vous suivantes
if ($chir_id = CValue::post("chir_id")) {
  CValue::setSession("chir_id", $chir_id);
}

// Consultation courante dans edit_consulation
if (CValue::post("del")) {
  CValue::setSession("selConsult");
}

$do = new CDoObjectAddEdit("CConsultation");
$do->doIt();
?>