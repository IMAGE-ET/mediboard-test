<?php 

/**
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$patient_id = CView::request("patient_id", "num");

CView::checkin();

$patient = new CPatient();
$patient->load($patient_id);

$dossier_medical = $patient->loadRefDossierMedical();
$dossier_medical->loadRefsAntecedents();

$smarty = new CSmartyDP();

$smarty->assign("dossier_medical", $dossier_medical);

$smarty->display("inc_atcd_majeur.tpl");