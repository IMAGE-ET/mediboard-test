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

$consultation_id = CValue::getOrSession("consultation_id");

$where = array("consultation_id" => "= '$consultation_id'");
$exam_nyha = new CExamNyha;
$exam_nyha->loadObject($where);
$exam_nyha->loadRefsNotes();

if (!$exam_nyha->_id) {
  $exam_nyha->consultation_id = $consultation_id;
}

$consultation = $exam_nyha->loadRefConsult();
$consultation->loadRefsFwd();
$consultation->loadRefConsultAnesth();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("exam_nyha" , $exam_nyha);

$smarty->display("exam_nyha.tpl");
