<?php
/**
 * $Id:$
 *
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();
$antecedent_id  = CValue::post("antecedent_id");
$callback       = CValue::post("reload");

$antecedent = new CAntecedent();
$antecedent->load($antecedent_id);
$antecedent->annule = 1;
if ($msg = $antecedent->store()) {
  CAppUI::stepAjax($msg, UI_MSG_WARNING);
}

$atcd_new = $antecedent;
$atcd_new->_id = null;
$atcd_new->annule = 0;
if ($msg = $atcd_new->store()) {
  CAppUI::stepAjax($msg, UI_MSG_WARNING);
}

$dossier_medical = $atcd_new->loadRefDossierMedical();
CAppUI::callbackAjax("Antecedent.editAntecedents", $dossier_medical->object_id, '', $callback, $atcd_new->_id);

echo CAppUI::getMsg();
CApp::rip();