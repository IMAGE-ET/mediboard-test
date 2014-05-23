<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$keyword = CValue::post('libelle_motif', '%');
$motifs = CMotifArretTravail::searchMotifsByLibelle($keyword);

foreach ($motifs as $_motif) {
  if ($_motif->type == 'groupe') {
    $_motif->loadChildren();
  }
  elseif ($_motif->type == 'motif') {
    $_motif->loadGroup();
  }
}

$smarty = new CSmartyDP();
$smarty->assign('motifs', $motifs);
$smarty->display('inc_motif_arret_travail_autocomplete.tpl');
