<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsRead();

$lang = CValue::getOrSession("lang", CCodeCIM10::LANG_FR);

$cim10 = new CCodeCIM10();
$chapter = $cim10->getSommaire($lang);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("lang"   , $lang);
$smarty->assign("cim10"  , $cim10);
$smarty->assign("chapter", $chapter);

$smarty->display("vw_idx_chapter.tpl");

?>