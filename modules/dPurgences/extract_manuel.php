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

$extractPassages = new CExtractPassages();

// Création du template
// Mettre car inclusion dans les modules externes
$smarty = new CSmartyDP("modules/dPurgences");

$smarty->assign("extractPassages", $extractPassages);
$smarty->assign("types"          , $types);

$smarty->display("extract_manuel.tpl");
