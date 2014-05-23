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

$motif_code = CValue::get('motif_code');

$duree = CDureeIndicativeArretTravail::loadForMotif($motif_code);

if ($duree) {
  $duree->loadCriteres();
  $duree->getDepth();
}

$smarty = new CSmartyDP();
$smarty->assign('duree', $duree);
$smarty->display('inc_duree_indicative_arret_travail.tpl');
