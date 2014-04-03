<?php 

/**
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */


CCanDo::checkAdmin();

$client_index   = new CSearch();
$client_index->createClient();
$index          = $client_index->loadIndex();
$mapping        = $index->getMapping();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("mapping", $mapping);
$smarty->assign("mappingjson", json_encode($mapping));
$smarty->display("vw_cartographie_mapping.tpl");