<?php 

/**
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$request = new CRequest();
$request->addSelect("categorie");
$request->addTable("motif_sfmu");
$request->addGroup("categorie");
$query = $request->makeSelect();

$motif_sfmu = new CMotifSFMU();
$ds = $motif_sfmu->getDS();
$categories = $ds->loadList($query);

$smarty = new CSmartyDP();
$smarty->assign("categories", $categories);
$smarty->display("inc_search_motif_sfmu.tpl");