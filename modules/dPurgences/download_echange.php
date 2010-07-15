<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$extract_passages_id = CValue::get("extract_passages_id");

$extractPassages = new CExtractPassages();
$extractPassages->load($extract_passages_id);

$rpu_sender = CExtractPassages::getRPUSender();
$extractPassages = $rpu_sender->loadExtractPassages($extractPassages);

$echange = utf8_decode($extractPassages->message);
header("Content-Disposition: attachment; filename={$extractPassages->type}-{$extract_passages_id}.xml");
header("Content-Type: text/plain; charset=".CApp::$encoding);
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header("Content-Length: ".strlen($echange));
echo $echange;

?>