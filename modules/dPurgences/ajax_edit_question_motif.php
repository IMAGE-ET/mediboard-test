<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$question_id  = CValue::get("question_id");
$motif_id     = CValue::get("motif_id");

$question = new CMotifQuestion();
$question->load($question_id);

if ($question_id) {
  $question->load($question_id);
}
else {
  $question->motif_id = $motif_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("question", $question);

$smarty->display("edit_question_motif.tpl");