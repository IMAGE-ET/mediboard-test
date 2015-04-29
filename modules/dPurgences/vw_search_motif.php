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
$rpu_id       = CValue::getOrSession("rpu_id");
$chapitre_id  = CValue::getOrSession("chapitre_id");
$search       = CValue::get("search");
$reload       = CValue::get("reload", 0);
$see_hors_ccmu= CValue::getOrSession("see_hors_ccmu", 0);

$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefMotif();
$rpu->loadRefsReponses();
$rpu->orderCtes();

$where = array();
if ($chapitre_id) {
  $where["chapitre_id"] = " = '$chapitre_id'";
}
if ($search) {
  $where[] = "nom LIKE '%$search%' or code_diag LIKE '%$search%'";
}

$motif  = new CMotif();
$motifs = $motif->loadList($where, "chapitre_id", null, "motif_id");

if (!$see_hors_ccmu) {
  foreach ($motifs as $_motif) {
    /* @var CMotif $_motif*/
    if (!($_motif->degre_min <= $rpu->_estimation_ccmu && $_motif->degre_max >= $rpu->_estimation_ccmu)) {
      unset($motifs[$_motif->_id]);
    }
  }
}

$chapitre = new CChapitreMotif();
/** @var CChapitreMotif[] $chapitres */
$chapitres = $chapitre->loadList(null, "nom");

$chapitres_search = $chapitres;
foreach ($motifs as $_motif) {
  /* @var CMotif $_motif*/
  $chapitres_search[$_motif->chapitre_id]->_ref_motifs[$_motif->_id] = $_motif;
}
foreach ($chapitres_search as $_chap) {
  if (!count($_chap->_ref_motifs)) {
    unset($chapitres_search[$_chap->_id]);
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("rpu"             , $rpu);
$smarty->assign("motifs"          , $motifs);
$smarty->assign("search"          , $search);
$smarty->assign("chapitres"       , $chapitres);
$smarty->assign("chapitre_id"     , $chapitre_id);
$smarty->assign("chapitres_search", $chapitres_search);
$smarty->assign("see_hors_ccmu"   , $see_hors_ccmu);

if (!$reload) {
  $smarty->display("vw_search_motif.tpl");
}
else {
  $smarty->assign("chapitres"   , $chapitres_search);
  $smarty->assign("readonly"   , true);
  $smarty->display("vw_list_motifs.tpl");
}
