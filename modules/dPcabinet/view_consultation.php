<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$consultation_id = CValue::get("consultation_id");

$consultation = new CConsultation;
$consultation->load($consultation_id);
$consultation->loadRefsFwd();

$prat = $consultation->_ref_plageconsult->_ref_chir;
$prat->loadRefs();

$patient = $consultation->_ref_patient;
$patient->loadRefs();

$today = CMbDT::date();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consultation", $consultation	);
$smarty->assign("patient"     , $patient      );
$smarty->assign("prat"        , $prat         );
$smarty->assign("today"       , $today        );

$smarty->display("view_consultation.tpl");
