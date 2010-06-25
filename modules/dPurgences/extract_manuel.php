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

$extractPassages = new CExtractPassages();

// Création du template
// Mettre car inclusion dans les modules externes
$smarty = new CSmartyDP("modules/dPurgences");

$smarty->assign("extractPassages", $extractPassages);
$smarty->assign("types"          , $types);

$smarty->display("extract_manuel.tpl");
?>
