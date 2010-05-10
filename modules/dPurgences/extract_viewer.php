<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$extract_passages_id = CValue::get("extract_passages_id");

$extractPassages = new CExtractPassages();
$extractPassages->load($extract_passages_id);

$rpu_sender = CExtractPassages::getRPUSender();
$extractPassages = $rpu_sender->loadExtractPassages($extractPassages);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("extractPassages", $extractPassages);
$smarty->display("extract_viewer.tpl");

?>