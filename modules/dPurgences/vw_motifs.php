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

$motif_id     = CValue::getOrSession("motif_id");
$chapitre_id  = CValue::getOrSession("chapitre_id");
$liste        = CValue::get("liste");

$motif  = new CMotif();
$motifs = $motif->loadList(null, "chapitre_id");

$chapitre = new CChapitreMotif();

/** @var CChapitreMotif[] $chapitres */
$chapitres = $chapitre->loadList(null, "nom");

foreach ($chapitres as $chap) {
  $chap->loadRefsMotifs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("motif_id"    , $motif_id);
$smarty->assign("chapitre_id" , $chapitre_id);
$smarty->assign("chapitres"   , $chapitres);

if ($liste == "motif") {
  $smarty->display("vw_list_motifs.tpl");
}
elseif ($liste == "chapitre") {
  $smarty->display("vw_list_chapitres.tpl");
}
else {
  $smarty->display("vw_motifs.tpl");
}
