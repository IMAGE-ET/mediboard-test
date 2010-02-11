<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$page = CValue::get('page', 0);

$extractPassages = new CExtractPassages();

$order = "date_extract DESC";
$total_passages = $extractPassages->countList(null, $order);
$listPassages   = $extractPassages->loadList(null, $order, "$page, 20");

$total_rpus = 0;
foreach ($listPassages as $_passage) {
  $_passage->loadRefsBack();
  $_passage->loadRefsFiles();
  
  $total_rpus += $_passage->_nb_rpus;
}

// Création du template
$smarty = new CSmartyDP("modules/dPurgences");
$smarty->assign("extractPassages", $extractPassages);
$smarty->assign("listPassages"   , $listPassages);

$smarty->assign("page"           , $page         );
$smarty->assign("total_passages" , $total_passages);

$smarty->assign("total_rpus"     , $total_rpus);

$smarty->display("vw_extract_passages.tpl");
?>
