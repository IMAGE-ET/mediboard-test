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

$extract_passages_id = CValue::get("extract_passages_id");

$extractPassages = new CExtractPassages();
$extractPassages->load($extract_passages_id);

$rpu_sender = CExtractPassages::getRPUSender();
$extractPassages = $rpu_sender->loadExtractPassages($extractPassages);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("extractPassages", $extractPassages);
$smarty->display("extract_viewer.tpl");
