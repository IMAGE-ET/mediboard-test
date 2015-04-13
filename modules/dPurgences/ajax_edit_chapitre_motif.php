<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$motif_id     = CValue::get("motif_id");
$chapitre_id  = CValue::get("chapitre_id");
$readonly     = CValue::get("readonly");

$motif  = new CMotif();
if ($motif_id) {
  $motif->load($motif_id);
  $motif->loadRefChapitre();
  $motif->loadRefsQuestions();
  if ($readonly) {
    $motif->loadRefsQuestionsByDegre();
  }
}

$chapitre = new CChapitreMotif();
$chapitres = $chapitre->loadList(null, "nom");
if ($chapitre_id) {
  $chapitre->load($chapitre_id);
  $chapitre->loadRefsMotifs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("motif"       , $motif);
$smarty->assign("motif_id"    , $motif_id);
$smarty->assign("chapitre"    , $chapitre);
$smarty->assign("chapitre_id" , $chapitre_id);
$smarty->assign("chapitres"   , $chapitres);
$smarty->assign("question"    , new CMotifQuestion());
$smarty->assign("readonly"    , $readonly);

$smarty->display("edit_chapitre_motif.tpl");
